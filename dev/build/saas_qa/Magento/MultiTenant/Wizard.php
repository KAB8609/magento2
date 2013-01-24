<?php
/**
 * Multi-tenant deployment wizard service model
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\MultiTenant;

class Wizard
{
    /**
     * @var array
     */
    private $_params;

    /**
     * @var string
     */
    private $_idFile;

    /**
     * Currently deployed tenants
     *
     * @var array
     */
    private $_tenants = array();

    /**
     * @var array
     */
    private $_dsn = array();

    /**
     * @var \PDO
     */
    private $_pdo;

    /**
     * @var \Magento_Shell
     */
    private $_shell;

    /**
     * @var \Zend_Log
     */
    private $_log;

    /**
     * @var CodeBase
     */
    private $_codeBase;

    /**
     * @var string
     */
    private $_workingDir;

    /**
     * @var string
     */
    private $_metaDir;

    /**
     * Initialize with required parameters
     *
     * @param \Zend_Log $logger
     * @param array $params
     * @param string $workingDir
     * @param string $metaInfoDir
     * @throws \Exception
     */
    public function __construct(\Zend_Log $logger, array $params, $workingDir, $metaInfoDir)
    {
        $this->_params = $params;
        if (empty($params['url-template']) || empty($params['dsn'])) {
            throw new \Exception('Not all required parameters are specified.');
        }
        if (!$metaInfoDir || !is_dir($metaInfoDir) || !is_writable($metaInfoDir)) {
            throw new \Exception("Meta information directory does not exist or not writable: '{$metaInfoDir}'");
        }
        $this->_log = $logger;
        $this->_shell = new \Magento_Shell($logger);
        $this->_codeBase = new CodeBase($this->_shell, $logger, $workingDir);
        $this->_workingDir = $workingDir;
        $this->_metaDir = $metaInfoDir;
        $this->_initTenants();
        $this->_initPdo($params['dsn']);
    }

    /**
     * Execute the wizard
     *
     * 1. If "cleanup" switch is specified, reset the working directory and uninstall all tenants
     *   - otherwise update code base and uninstall specified tenants (if specified)
     * 2. Perform upgrade for existing tenants
     * 3. Install new tenants (if specified)
     */
    public function execute()
    {
        $this->_codeBase->setLock();
        if (isset($this->_params['cleanup'])) {
            if ($this->_tenants) {
                $this->uninstall(array_keys($this->_tenants));
            }
            $this->_codeBase->resetWorkingDir();
        } else {
            if (!empty($this->_params['uninstall'])) {
                $this->uninstall($this->_extractIds($this->_params['uninstall']));
            }
        }

        /** @var $tenant Tenant */
        foreach ($this->_tenants as $tenant) {
            $this->_log->log("=== Upgrade Tenant: '{$tenant->getId()}' ===", \Zend_Log::INFO);
            $this->_upgrade($tenant);
            $this->_log->log('', \Zend_Log::INFO);
        }

        if (!empty($this->_params['install'])) {
            $this->install($this->_extractIds($this->_params['install']));
        }
        $this->_codeBase->setLock(false);
    }

    /**
     * Uninstall tenants by specified IDs
     *
     * @param array $ids
     */
    public function uninstall(array $ids)
    {
        $this->_codeBase->setLock();
        foreach ($ids as $id) {
            $this->_log->log("=== Uninstall Tenant: '{$id}' ===", \Zend_Log::INFO);
            if (!isset($this->_tenants[$id])) {
                $this->_log->log(
                    "Tenant '{$id}' is not registered as installed, ignoring uninstall request.", \Zend_Log::INFO
                );
            } else {
                /** @var $tenant Tenant */
                $tenant = $this->_tenants[$id];
                $this->_dropDb($tenant);
                $this->_codeBase->removeDir($this->_getMediaUri($tenant));
                $this->_codeBase->removeDir($tenant->getVarDirName());
                unlink("{$this->_metaDir}/{$tenant->getLocalXmlFilename()}");
                unset($this->_tenants[$id]);
            }
            $this->_log->log('', \Zend_Log::INFO);
        }
        $this->_codeBase->setLock(false);
    }

    /**
     * Install tenants by specified IDs
     *
     * @param array $ids
     */
    public function install(array $ids)
    {
        $this->_codeBase->setLock();
        foreach ($ids as $id) {
            $tenant = $this->_addTenant($id);
            $this->_log->log("=== Install Tenant: '{$tenant->getId()}' ===", \Zend_Log::INFO);
            $this->_install($tenant);
            $this->_log->log('', \Zend_Log::INFO);
        }
        $this->_codeBase->setLock(false);
    }

    /**
     * Read tenants collection from persistent storage
     */
    private function _initTenants()
    {
        $this->_idFile = "{$this->_metaDir}/tenants.txt";
        if (is_file($this->_idFile)) {
            foreach ($this->_extractIds(file_get_contents($this->_idFile)) as $id) {
                $this->_addTenant($id);
            }
        }
    }

    /**
     * Add or replace tenant with specified ID
     *
     * @param string $identifier
     * @return Tenant
     */
    private function _addTenant($identifier)
    {
        $this->_tenants[$identifier] = new Tenant($identifier, $this->_params['url-template']);
        return $this->_tenants[$identifier];
    }

    /**
     * Transform comma-separated string of IDs into array
     *
     * @param string $idString
     * @return array
     * @throws \Exception
     */
    private function _extractIds($idString)
    {
        if (0 == strlen($idString)) {
            throw new \Exception("Invalid ID string specified: '{$idString}'");
        }
        return explode(',', $idString);
    }

    /**
     * Parse DSN and instantiate PDO
     *
     * @param string $dsnString
     * @throws \Exception
     */
    private function _initPdo($dsnString)
    {
        $dsn = parse_url($dsnString);
        if (!$dsn || empty($dsn['host']) || 'mysql' !== $dsn['scheme'] || empty($dsn['user'])) {
            throw new \Exception("Malformed database connection string: '{$dsnString}'");
        }
        if (empty($dsn['pass'])) {
            $dsn['pass'] = '';
        }
        $this->_dsn = $dsn;
        $this->_pdo = new \PDO('mysql:host=' . $dsn['host'], $dsn['user'], $dsn['pass']);
    }

    /**
     * Installation sub-routine
     *
     * Create required directories, perform Magento installation into tenant DB
     *
     * @param Tenant $tenant
     */
    private function _install(Tenant $tenant)
    {
        $varDir = $this->_codeBase->recreateDir($tenant->getVarDirName());
        $mediaUri = $this->_getMediaUri($tenant);
        $mediaDir = $this->_codeBase->recreateDir($mediaUri);
        $this->_recreateDb($tenant);

        // run install.php and obtain generated base configuration
        $options = array(
            'license_agreement_accepted' => 'yes',
            'locale'                     => 'en_US',
            'timezone'                   => 'America/Los_Angeles',
            'default_currency'           => 'USD',
            'db_host'                    => $this->_dsn['host'],
            'db_name'                    => $tenant->getDbName(),
            'db_user'                    => $this->_dsn['user'],
            'db_pass'                    => $this->_dsn['pass'],
            'use_secure'                 => 'yes',
            'use_secure_admin'           => 'yes',
            'admin_no_form_key'          => 'yes',
            'use_rewrites'               => 'no',
            'admin_lastname'             => $tenant->getId(),
            'admin_firstname'            => $tenant->getId(),
            'admin_email'                => "{$tenant->getId()}@example.com",
            'admin_username'             => $tenant->getId(),
            'admin_password'             => '123123q',
            'url'                        => $tenant->getUrl(),
            'skip_url_validation'        => true,
            'secure_base_url'            => $tenant->getUrl(),
            'session_save'               => 'db',
            'cleanup_database'           => true,
            'install_option_uris'        => base64_encode(serialize(array('media' => $mediaUri))),
            'install_option_dirs'        => base64_encode(serialize(array('var' => $varDir, 'media' => $mediaDir))),
        );
        $command = 'php -f %s --';
        $arguments = array("{$this->_workingDir}/dev/shell/install.php");
        foreach ($options as $key => $value) {
            $command .= ' --%s %s';
            $arguments[] = $key;
            $arguments[] = $value;
        }
        $this->_shell->execute($command, $arguments);
        // hack to get the local.xml out of the code base (entire installer requires refactoring to make it clean)
        $origLocalXml = "{$this->_workingDir}/app/etc/local.xml";
        $metaLocalXml = "{$this->_metaDir}/{$tenant->getLocalXmlFilename()}";
        $this->_log->log("rename({$origLocalXml}, {$metaLocalXml})", \Zend_Log::INFO);
        rename($origLocalXml, $metaLocalXml);
    }

    /**
     * Compose media URI
     *
     * @param Tenant $tenant
     * @return string
     */
    private function _getMediaUri(Tenant $tenant)
    {
        return "pub/{$tenant->getMediaDirName()}";
    }

    /**
     * Perform upgrade for specified tenant
     *
     * @param Tenant $tenant
     */
    private function _upgrade(Tenant $tenant)
    {
        $this->_codeBase->recreateDir($tenant->getVarDirName());
        $localXml = "{$this->_metaDir}/{$tenant->getLocalXmlFilename()}";
        $mediaUri = $this->_getMediaUri($tenant);
        $this->_shell->execute(
            'php -f %s -- --local-xml=%s --media-uri=%s --media-dir=%s --var-dir=%s', array(
                "{$this->_workingDir}/dev/build/saas_qa/upgrade.php",
                $localXml,
                $mediaUri,
                "{$this->_workingDir}/{$mediaUri}",
                "{$this->_workingDir}/{$tenant->getVarDirName()}"
            )
        );
    }

    /**
     * Drop and create DB of specified tenant
     *
     * @param Tenant $tenant
     */
    private function _recreateDb(Tenant $tenant)
    {
        $this->_dropDb($tenant);
        $query = "CREATE DATABASE `{$tenant->getDbName()}`";
        $this->_log->log($query, \Zend_Log::INFO);
        $this->_pdo->query($query);
    }

    /**
     * Drop tenant DB
     *
     * @param Tenant $tenant
     */
    private function _dropDb(Tenant $tenant)
    {
        $query = "DROP DATABASE IF EXISTS `{$tenant->getDbName()}`";
        $this->_log->log($query, \Zend_Log::INFO);
        $this->_pdo->query($query);
    }

    /**
     * Persist current identifiers of tenants and output useful information about them
     *
     * If there are tenants available:
     * - Output all URLs available for all tenants to the log
     * - Persist all tenant IDs for further reuse
     */
    public function __destruct()
    {
        if (empty($this->_tenants)) {
            $this->_log->log('There are no tenants deployed at the moment.', \Zend_Log::INFO);
            if (file_exists($this->_idFile)) {
                unlink($this->_idFile);
            }
        } else {
            $urls = array();
            /** @var $tenant Tenant */
            foreach ($this->_tenants as $tenant) {
                $urls[] = $tenant->getUrl();
            }
            file_put_contents($this->_idFile, implode(',', array_keys($this->_tenants)));
            $this->_log->log("Deployed tenant URLs:\n" . implode("\n", $urls), \Zend_Log::INFO);
            $this->_log->log("Tenant IDs are recorded to file '{$this->_idFile}' for future reuse.", \Zend_Log::INFO);
        }
    }
}
