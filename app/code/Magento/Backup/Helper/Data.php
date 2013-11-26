<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup data helper
 */
namespace Magento\Backup\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Backup type constant for database backup
     */
    const TYPE_DB = 'db';

    /**
     * Backup type constant for filesystem backup
     */
    const TYPE_FILESYSTEM = 'filesystem';

    /**
     * Backup type constant for full system backup(database + filesystem)
     */
    const TYPE_SYSTEM_SNAPSHOT = 'snapshot';

    /**
     * Backup type constant for media and database backup
     */
    const TYPE_MEDIA = 'media';

    /**
     * Backup type constant for full system backup excluding media folder
     */
    const TYPE_SNAPSHOT_WITHOUT_MEDIA = 'nomedia';

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Cache\ConfigInterface
     */
    protected $_cacheConfig;

    /**
     * @var \Magento\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;
    
    /**
     * Directory model
     *
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * Index resource process collection factory
     *
     * @var \Magento\Index\Model\Resource\Process\CollectionFactory
     */
    protected $_processFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\Cache\ConfigInterface $cacheConfig
     * @param \Magento\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\App\Dir $dir
     * @param \Magento\Index\Model\Resource\Process\CollectionFactory $processFactory
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Filesystem $filesystem,
        \Magento\AuthorizationInterface $authorization,
        \Magento\Cache\ConfigInterface $cacheConfig,
        \Magento\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\App\Dir $dir,
        \Magento\Index\Model\Resource\Process\CollectionFactory $processFactory
    ) {
        parent::__construct($context);
        $this->_authorization = $authorization;
        $this->_filesystem = $filesystem;        
        $this->_cacheConfig = $cacheConfig;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_dir = $dir;
    }

    /**
     * Get all possible backup type values with descriptive title
     *
     * @return array
     */
    public function getBackupTypes()
    {
        return array(
            self::TYPE_DB                     => __('Database'),
            self::TYPE_MEDIA                  => __('Database and Media'),
            self::TYPE_SYSTEM_SNAPSHOT        => __('System'),
            self::TYPE_SNAPSHOT_WITHOUT_MEDIA => __('System (excluding Media)')
        );
    }

    /**
     * Get all possible backup type values
     *
     * @return array
     */
    public function getBackupTypesList()
    {
        return array(
            self::TYPE_DB,
            self::TYPE_SYSTEM_SNAPSHOT,
            self::TYPE_SNAPSHOT_WITHOUT_MEDIA,
            self::TYPE_MEDIA
        );
    }

    /**
     * Get default backup type value
     *
     * @return string
     */
    public function getDefaultBackupType()
    {
        return self::TYPE_DB;
    }

    /**
     * Get directory path where backups stored
     *
     * @return string
     */
    public function getBackupsDir()
    {
        return $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/backups';
    }

    /**
     * Get backup file extension by backup type
     *
     * @param string $type
     * @return string
     */
    public function getExtensionByType($type)
    {
        $extensions = $this->getExtensions();
        return isset($extensions[$type]) ? $extensions[$type] : '';
    }

    /**
     * Get all types to extensions map
     *
     * @return array
     */
    public function getExtensions()
    {
        return array(
            self::TYPE_SYSTEM_SNAPSHOT => 'tgz',
            self::TYPE_SNAPSHOT_WITHOUT_MEDIA => 'tgz',
            self::TYPE_MEDIA => 'tgz',
            self::TYPE_DB => 'gz'
        );
    }

    /**
     * Generate backup download name
     *
     * @param \Magento\Backup\Model\Backup $backup
     * @return string
     */
    public function generateBackupDownloadName(\Magento\Backup\Model\Backup $backup)
    {
        $additionalExtension = $backup->getType() == self::TYPE_DB ? '.sql' : '';
        return $backup->getType() . '-' . date('YmdHis', $backup->getTime()) . $additionalExtension . '.'
            . $this->getExtensionByType($backup->getType());
    }

    /**
     * Check Permission for Rollback
     *
     * @return boolean
     */
    public function isRollbackAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Backup::rollback' );
    }

    /**
     * Get paths that should be ignored when creating system snapshots
     *
     * @return array
     */
    public function getBackupIgnorePaths()
    {
        return array(
            '.git',
            '.svn',
            'maintenance.flag',
            $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/session',
            $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/cache',
            $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/full_page_cache',
            $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/locks',
            $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/log',
            $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/report',
        );
    }

    /**
     * Get paths that should be ignored when rolling back system snapshots
     *
     * @return array
     */
    public function getRollbackIgnorePaths()
    {
        return array(
            '.svn',
            '.git',
            'maintenance.flag',
            $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/session',
            $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/locks',
            $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/log',
            $this->_dir->getDir(\Magento\App\Dir::VAR_DIR) . '/report',
            $this->_dir->getDir() . '/errors',
            $this->_dir->getDir() . '/index.php',
        );
    }

    /**
     * Put store into maintenance mode
     *
     * @return bool
     */
    public function turnOnMaintenanceMode()
    {
        $maintenanceFlagFile = $this->getMaintenanceFlagFilePath();
        $result = $this->_filesystem->write(
            $maintenanceFlagFile,
            'maintenance',
            $this->_dir->getDir()
        );

        return $result !== false;
    }

    /**
     * Turn off store maintenance mode
     */
    public function turnOffMaintenanceMode()
    {
        $maintenanceFlagFile = $this->getMaintenanceFlagFilePath();
        $this->_filesystem->delete($maintenanceFlagFile, $this->_dir->getDir());
    }

    /**
     * Get backup create success message by backup type
     *
     * @param string $type
     * @return string
     */
    public function getCreateSuccessMessageByType($type)
    {
        $messagesMap = array(
            self::TYPE_SYSTEM_SNAPSHOT => __('The system backup has been created.'),
            self::TYPE_SNAPSHOT_WITHOUT_MEDIA => __('The system backup (excluding media) has been created.'),
            self::TYPE_MEDIA => __('The database and media backup has been created.'),
            self::TYPE_DB => __('The database backup has been created.')
        );

        if (!isset($messagesMap[$type])) {
            return;
        }

        return $messagesMap[$type];
    }

    /**
     * Get path to maintenance flag file
     *
     * @return string
     */
    protected function getMaintenanceFlagFilePath()
    {
        return $this->_dir->getDir() . '/maintenance.flag';
    }

    /**
     * Invalidate Cache
     *
     * @return \Magento\Backup\Helper\Data
     */
    public function invalidateCache()
    {
        if ($cacheTypes = $this->_cacheConfig->getTypes()) {
            $cacheTypesList = array_keys($cacheTypes);
            $this->_cacheTypeList->invalidate($cacheTypesList);
        }
        return $this;
    }

    /**
     * Invalidate Indexer
     *
     * @return \Magento\Backup\Helper\Data
     */
    public function invalidateIndexer()
    {
        foreach ($this->_processFactory->create() as $process) {
            $process->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }
        return $this;
    }

    /**
     * Creates backup's display name from it's name
     *
     * @param string $name
     * @return string
     */
    public function nameToDisplayName($name)
    {
        return str_replace('_', ' ', $name);
    }

    /**
     * Extracts information from backup's filename
     *
     * @param string $filename
     * @return \Magento\Object
     */
    public function extractDataFromFilename($filename)
    {
        $extensions = $this->getExtensions();

        $filenameWithoutExtension = $filename;

        foreach ($extensions as $extension) {
            $filenameWithoutExtension = preg_replace('/' . preg_quote($extension, '/') . '$/', '',
                $filenameWithoutExtension
            );
        }

        $filenameWithoutExtension = substr($filenameWithoutExtension, 0, strrpos($filenameWithoutExtension, "."));

        list($time, $type) = explode("_", $filenameWithoutExtension);

        $name = str_replace($time . '_' . $type, '', $filenameWithoutExtension);

        if (!empty($name)) {
            $name = substr($name, 1);
        }

        $result = new \Magento\Object();
        $result->addData(array(
            'name' => $name,
            'type' => $type,
            'time' => $time
        ));

        return $result;
    }
}
