<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design Tile Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Tile extends Saas_Launcher_Block_Adminhtml_Tile
{
    /**
     * Launcher Helper
     *
     * @var Saas_Launcher_Helper_Data
     */
    protected  $_launcherHelper;

    /**
     * @var Mage_Core_Model_Theme_Service
     */
    protected $_themeService;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Saas_Launcher_Helper_Data $launcherHelper
     * @param Mage_Core_Model_Theme_Service $themeService
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Saas_Launcher_Helper_Data $launcherHelper,
        Mage_Core_Model_Theme_Service $themeService,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_launcherHelper = $launcherHelper;
        $this->_themeService = $themeService;
    }

    /**
     * Checks Logo is uploaded for current Store View
     *
     * @return bool
     */
    public function getIsLogoUploaded()
    {
        $logo = $this->_storeConfig->getConfig(Saas_Launcher_Model_Storelauncher_Design_SaveHandler::XML_PATH_LOGO,
            $this->_launcherHelper->getCurrentStoreView());
        return !empty($logo);
    }

    /**
     * Get Theme Name
     *
     * @return string
     */
    public function getThemeName()
    {
        $themeName = '';
        $themeId = $this->_storeConfig->getConfig(Mage_Core_Model_Design_PackageInterface::XML_PATH_THEME_ID,
            $this->_launcherHelper->getCurrentStoreView());
        if ($themeId) {
            $themeName = $this->_themeService->getThemeById($themeId)->getThemeTitle();
        }

        return $themeName;
    }
}
