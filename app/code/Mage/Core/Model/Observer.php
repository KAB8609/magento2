<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core Observer model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Observer
{
    /**
     * @var Mage_Core_Model_Cache_Frontend_Pool
     */
    private $_cacheFrontendPool;

    /**
     * @var Mage_Core_Model_Theme
     */
    private $_currentTheme;

    /**
     * @var Mage_Core_Model_Page_Asset_Collection
     */
    private $_pageAssets;

    /**
     * Instance of config model
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_Cache_Frontend_Pool $cacheFrontendPool
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Page $page
     * @param Mage_Core_Model_ConfigInterface $config
     */
    public function __construct(
        Mage_Core_Model_Cache_Frontend_Pool $cacheFrontendPool,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Page $page,
        Mage_Core_Model_ConfigInterface $config
    ) {
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_currentTheme = $designPackage->getDesignTheme();
        $this->_pageAssets = $page->getAssets();
        $this->_config = $config;
    }

    /**
     * Check if synchronize process is finished and generate notification message
     *
     * @param  Varien_Event_Observer $observer
     * @return Mage_Core_Model_Observer
     */
    public function addSynchronizeNotification(Varien_Event_Observer $observer)
    {
        $adminSession = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        if (!$adminSession->hasSyncProcessStopWatch()) {
            $flag = Mage::getSingleton('Mage_Core_Model_File_Storage')->getSyncFlag();
            $state = $flag->getState();
            if ($state == Mage_Core_Model_File_Storage_Flag::STATE_RUNNING) {
                $syncProcessStopWatch = true;
            } else {
                $syncProcessStopWatch = false;
            }

            $adminSession->setSyncProcessStopWatch($syncProcessStopWatch);
        }
        $adminSession->setSyncProcessStopWatch(false);

        if (!$adminSession->getSyncProcessStopWatch()) {
            if (!isset($flag)) {
                $flag = Mage::getSingleton('Mage_Core_Model_File_Storage')->getSyncFlag();
            }

            $state = $flag->getState();
            if ($state == Mage_Core_Model_File_Storage_Flag::STATE_FINISHED) {
                $flagData = $flag->getFlagData();
                if (isset($flagData['has_errors']) && $flagData['has_errors']) {
                    $severity       = Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;
                    $title          = Mage::helper('Mage_Adminhtml_Helper_Data')->__('An error has occured while syncronizing media storages.');
                    $description    = Mage::helper('Mage_Adminhtml_Helper_Data')->__('One or more media files failed to be synchronized during the media storages syncronization process. Refer to the log file for details.');
                } else {
                    $severity       = Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE;
                    $title          = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Media storages synchronization has completed!');
                    $description    = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Synchronization of media storages has been successfully completed.');
                }

                $date = date('Y-m-d H:i:s');
                Mage::getModel('Mage_AdminNotification_Model_Inbox')->parse(array(
                    array(
                        'severity'      => $severity,
                        'date_added'    => $date,
                        'title'         => $title,
                        'description'   => $description,
                        'url'           => '',
                        'internal'      => true
                    )
                ));

                $flag->setState(Mage_Core_Model_File_Storage_Flag::STATE_NOTIFIED)->save();
            }

            $adminSession->setSyncProcessStopWatch(false);
        }

        return $this;
    }

    /**
     * Cron job method to clean old cache resources
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function cleanCache(Mage_Cron_Model_Schedule $schedule)
    {
        /** @var $cacheFrontend Magento_Cache_FrontendInterface */
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            // Magento cache frontend does not support the 'old' cleaning mode, that's why backend is used directly
            $cacheFrontend->getBackend()->clean(Zend_Cache::CLEANING_MODE_OLD);
        }
    }

    /**
     * Theme registration
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Core_Model_Observer
     */
    public function themeRegistration(Varien_Event_Observer $observer)
    {
        $baseDir = $observer->getEvent()->getBaseDir();
        $pathPattern = $observer->getEvent()->getPathPattern();
        try {
            Mage::getObjectManager()->get('Mage_Core_Model_Theme_Registration')->register($baseDir, $pathPattern);
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applyThemeCustomization(Varien_Event_Observer $observer)
    {
        /** @var $themeFile Mage_Core_Model_Theme_File */
        foreach ($this->_currentTheme->getFiles() as $themeFile) {
            $asset = $themeFile->getAsset();
            if ($asset) {
                $this->_pageAssets->add($themeFile->getFilePath(), $asset);
            }
        }
    }

    /**
     * Rebuild whole config and save to fast storage
     *
     * @param  Varien_Event_Observer $observer
     * @return Mage_Core_Model_Observer
     */
    public function processReinitConfig(Varien_Event_Observer $observer)
    {
        $this->_config->reinit();
        return $this;
    }
}
