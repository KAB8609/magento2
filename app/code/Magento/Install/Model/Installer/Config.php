<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config installer
 * @category   Magento
 * @package    Magento_Install
 */
namespace Magento\Install\Model\Installer;

class Config extends \Magento\Install\Model\Installer\AbstractInstaller
{
    const TMP_INSTALL_DATE_VALUE= 'd-d-d-d-d';
    const TMP_ENCRYPT_KEY_VALUE = 'k-k-k-k-k';

    /**
     * Path to local configuration file
     *
     * @var string
     */
    protected $_localConfigFile;

    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * Resource configuration
     *
     * @var \Magento\Core\Model\Config\Resource
     */
    protected $_resourceConfig;

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dirs;

    protected $_configData = array();

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Core\Model\Dir $dirs
     * @param \Magento\Core\Model\Config\Resource $resourceConfig
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Core\Model\Dir $dirs,
        \Magento\Core\Model\Config\Resource $resourceConfig,
        \Magento\Filesystem $filesystem
    ) {
        $this->_localConfigFile = $dirs->getDir(\Magento\Core\Model\Dir::CONFIG) . DIRECTORY_SEPARATOR . 'local.xml';
        $this->_dirs = $dirs;
        $this->_request = $request;
        $this->_resourceConfig = $resourceConfig;
        $this->_filesystem = $filesystem;
    }

    public function setConfigData($data)
    {
        if (is_array($data)) {
            $this->_configData = $data;
        }
        return $this;
    }

    public function getConfigData()
    {
        return $this->_configData;
    }

    /**
     * Generate installation data and record them into local.xml using local.xml.template
     */
    public function install()
    {
        $data = $this->getConfigData();

        $defaults = array(
            'root_dir' => $this->_dirs->getDir(\Magento\Core\Model\Dir::ROOT),
            'app_dir'  => $this->_dirs->getDir(\Magento\Core\Model\Dir::APP),
            'var_dir'  => $this->_dirs->getDir(\Magento\Core\Model\Dir::VAR_DIR),
            'base_url' => $this->_request->getDistroBaseUrl(),
        );
        foreach ($defaults as $index => $value) {
            if (!isset($data[$index])) {
                $data[$index] = $value;
            }
        }

        if (isset($data['unsecure_base_url'])) {
            $data['unsecure_base_url'] .= substr($data['unsecure_base_url'], -1) != '/' ? '/' : '';
            if (strpos($data['unsecure_base_url'], 'http') !== 0) {
                $data['unsecure_base_url'] = 'http://' . $data['unsecure_base_url'];
            }
            if (!$this->_getInstaller()->getDataModel()->getSkipBaseUrlValidation()) {
                $this->_checkUrl($data['unsecure_base_url']);
            }
        }
        if (isset($data['secure_base_url'])) {
            $data['secure_base_url'] .= substr($data['secure_base_url'], -1) != '/' ? '/' : '';
            if (strpos($data['secure_base_url'], 'http') !== 0) {
                $data['secure_base_url'] = 'https://' . $data['secure_base_url'];
            }

            if (!empty($data['use_secure'])
                && !$this->_getInstaller()->getDataModel()->getSkipUrlValidation()) {
                $this->_checkUrl($data['secure_base_url']);
            }
        }

        $data['date']   = self::TMP_INSTALL_DATE_VALUE;
        $data['key']    = self::TMP_ENCRYPT_KEY_VALUE;
        $data['var_dir'] = $data['root_dir'] . '/var';

        $data['use_script_name'] = isset($data['use_script_name']) ? 'true' : 'false';

        $this->_getInstaller()->getDataModel()->setConfigData($data);

        $path = $this->_dirs->getDir(\Magento\Core\Model\Dir::CONFIG) . DIRECTORY_SEPARATOR . 'local.xml.template';
        $contents = $this->_filesystem->read($path);
        foreach ($data as $index => $value) {
            $contents = str_replace('{{' . $index . '}}', '<![CDATA[' . $value . ']]>', $contents);
        }

        $this->_filesystem->write($this->_localConfigFile, $contents);
        $this->_filesystem->changePermissions($this->_localConfigFile, 0777);
    }

    public function getFormData()
    {
        $uri = \Zend_Uri::factory(\Mage::getBaseUrl('web'));

        $baseUrl = $uri->getUri();
        if ($uri->getScheme() !== 'https') {
            $uri->setPort(null);
            $baseSecureUrl = str_replace('http://', 'https://', $uri->getUri());
        } else {
            $baseSecureUrl = $uri->getUri();
        }

        $connectDefault = $this->_resourceConfig
                ->getResourceConnectionConfig(\Magento\Core\Model\Resource::DEFAULT_SETUP_RESOURCE);

        $data = new \Magento\Object();
        $data->setDbHost($connectDefault->host)
            ->setDbName($connectDefault->dbname)
            ->setDbUser($connectDefault->username)
            ->setDbModel($connectDefault->model)
            ->setDbPass('')
            ->setSecureBaseUrl($baseSecureUrl)
            ->setUnsecureBaseUrl($baseUrl)
            ->setBackendFrontname('backend')
            ->setEnableCharts('1')
        ;
        return $data;
    }

    /**
     * Check validity of a base URL
     *
     * @param string $baseUrl
     * @throws \Exception
     */
    protected function _checkUrl($baseUrl)
    {
        try {
            $pubLibDir = $this->_dirs->getDir(\Magento\Core\Model\Dir::PUB_LIB);
            $staticFile = $this->_findFirstFileRelativePath($pubLibDir, '/.+\.(html?|js|css|gif|jpe?g|png)$/');
            $staticUrl = $baseUrl . $this->_dirs->getUri(\Magento\Core\Model\Dir::PUB_LIB) . '/' . $staticFile;
            $client = new \Magento\HTTP\ZendClient($staticUrl);
            $response = $client->request('GET');
        } catch (\Exception $e){
            $this->_getInstaller()->getDataModel()->addError(
                __('The URL "%1" is not accessible.', $baseUrl)
            );
            throw $e;
        }
        if ($response->getStatus() != 200) {
            $this->_getInstaller()->getDataModel()->addError(
                __('The URL "%1" is invalid.', $baseUrl)
            );
            \Mage::throwException(__('Response from the server is invalid.'));
        }
    }

    /**
     * Find a relative path to a first file located in a directory or its descendants
     *
     * @param string $dir \Directory to search for a file within
     * @param string $pattern PCRE pattern a file name has to match
     * @return string|null
     */
    protected function _findFirstFileRelativePath($dir, $pattern = '/.*/')
    {
        $childDirs = array();
        foreach (scandir($dir) as $itemName) {
            if ($itemName == '.' || $itemName == '..') {
                continue;
            }
            $itemPath = $dir . DIRECTORY_SEPARATOR . $itemName;
            if (is_file($itemPath)) {
                if (preg_match($pattern, $itemName)) {
                    return $itemName;
                }
            } else {
                $childDirs[$itemName] = $itemPath;
            }
        }
        foreach ($childDirs as $dirName => $dirPath) {
            $filePath = $this->_findFirstFileRelativePath($dirPath, $pattern);
            if ($filePath) {
                return $dirName . '/' . $filePath;
            }
        }
        return null;
    }

    public function replaceTmpInstallDate($date = 'now')
    {
        $stamp    = strtotime((string) $date);
        $localXml = $this->_filesystem->read($this->_localConfigFile);
        $localXml = str_replace(self::TMP_INSTALL_DATE_VALUE, date('r', $stamp), $localXml);
        $this->_filesystem->write($this->_localConfigFile, $localXml);

        return $this;
    }

    public function replaceTmpEncryptKey($key)
    {
        $localXml = $this->_filesystem->read($this->_localConfigFile);
        $localXml = str_replace(self::TMP_ENCRYPT_KEY_VALUE, $key, $localXml);
        $this->_filesystem->write($this->_localConfigFile, $localXml);

        return $this;
    }
}
