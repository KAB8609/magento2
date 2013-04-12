<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design editor state model
 */
class Mage_DesignEditor_Model_State
{
    /**#@+
     * Name of layout classes that will be used as main layout
     */
    const LAYOUT_DESIGN_CLASS_NAME     = 'Mage_DesignEditor_Model_Layout';
    const LAYOUT_NAVIGATION_CLASS_NAME = 'Mage_Core_Model_Layout';
    /**#@-*/

    /**#@+
     * Url model classes that will be used instead of Mage_Core_Model_Url in different vde modes
     */
    const URL_MODEL_NAVIGATION_MODE_CLASS_NAME = 'Mage_DesignEditor_Model_Url_NavigationMode';
    const URL_MODEL_DESIGN_MODE_CLASS_NAME     = 'Mage_DesignEditor_Model_Url_DesignMode';
    /**#@-*/

    /**#@+
     * Layout update resource models
     */
    const LAYOUT_UPDATE_RESOURCE_MODEL_CORE_CLASS_NAME = 'Mage_Core_Model_Resource_Layout_Update';
    const LAYOUT_UPDATE_RESOURCE_MODEL_VDE_CLASS_NAME  = 'Mage_DesignEditor_Model_Resource_Layout_Update';
    /**#@-*/

    /**#@+
     * Import behaviors
     */
    const MODE_DESIGN     = 'design';
    const MODE_NAVIGATION = 'navigation';
    /**#@-*/

    /**#@+
     * Session keys
     */
    const CURRENT_HANDLE_SESSION_KEY = 'vde_current_handle';
    const CURRENT_URL_SESSION_KEY    = 'vde_current_url';
    const CURRENT_MODE_SESSION_KEY   = 'vde_current_mode';
    /**#@-*/

    /**
     * Session key of editable theme
     */
    const CURRENT_THEME_SESSION_KEY = 'vde_theme_id';

    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @var Mage_Core_Model_Layout_Factory
     */
    protected $_layoutFactory;

    /**
     * @var Mage_DesignEditor_Model_Url_Factory
     */
    protected $_urlModelFactory;

    /**
     * Application Cache Manager
     *
     * @var Mage_Core_Model_Cache
     */
    protected $_cacheManager;

    /**
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_dataHelper;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Design_PackageInterface
     */
    protected $_design;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_application;

    /**
     * @param Mage_Backend_Model_Session $backendSession
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_DesignEditor_Model_Url_Factory $urlModelFactory
     * @param Mage_Core_Model_Cache $cacheManager
     * @param Mage_DesignEditor_Helper_Data $dataHelper
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Design_PackageInterface $design
     * @param Mage_Core_Model_App $application
     */
    public function __construct(
        Mage_Backend_Model_Session $backendSession,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_DesignEditor_Model_Url_Factory $urlModelFactory,
        Mage_Core_Model_Cache $cacheManager,
        Mage_DesignEditor_Helper_Data $dataHelper,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Design_PackageInterface $design,
        Mage_Core_Model_App $application
    ) {
        $this->_backendSession  = $backendSession;
        $this->_layoutFactory   = $layoutFactory;
        $this->_urlModelFactory = $urlModelFactory;
        $this->_cacheManager    = $cacheManager;
        $this->_dataHelper      = $dataHelper;
        $this->_objectManager   = $objectManager;
        $this->_design          = $design;
        $this->_application     = $application;
    }

    /**
     * Update system data for current VDE environment
     *
     * @param string $areaCode
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Varien_ActionAbstract $controller
     */
    public function update(
        $areaCode,
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Varien_ActionAbstract $controller
    ) {
        $handle = $request->getParam('handle', '');
        if (empty($handle)) {
            $mode = self::MODE_NAVIGATION;

            if (!$request->isAjax()) {
                $this->_backendSession->setData(self::CURRENT_HANDLE_SESSION_KEY, $controller->getFullActionName());
                $this->_backendSession->setData(self::CURRENT_URL_SESSION_KEY, $request->getPathInfo());
            }
        } else {
            $mode = self::MODE_DESIGN;
        }

        $this->_backendSession->setData(self::CURRENT_MODE_SESSION_KEY, $mode);
        $this->_injectUrlModel($mode);
        $this->_injectLayout($mode, $areaCode);
        $this->_injectLayoutUpdateResourceModel();
        $this->_setTheme();
        $this->_disableCache();
    }

    /**
     * Reset VDE state data
     *
     * @return Mage_DesignEditor_Model_State
     */
    public function reset()
    {
        $this->_backendSession->unsetData(self::CURRENT_HANDLE_SESSION_KEY)
            ->unsetData(self::CURRENT_URL_SESSION_KEY)
            ->unsetData(self::CURRENT_MODE_SESSION_KEY)
            ->unsetData(self::CURRENT_THEME_SESSION_KEY);

        return $this;
    }

    /**
     * Create layout instance that will be used as main layout for whole system
     *
     * @param string $mode
     * @param string $areaCode
     */
    protected function _injectLayout($mode, $areaCode)
    {
        switch ($mode) {
            case self::MODE_DESIGN:
                $this->_layoutFactory->createLayout(array('area' => $areaCode), self::LAYOUT_DESIGN_CLASS_NAME);
                break;
            case self::MODE_NAVIGATION:
            default:
                $this->_layoutFactory->createLayout(array('area' => $areaCode), self::LAYOUT_NAVIGATION_CLASS_NAME);
                break;
        }
    }

    /**
     * Create url model instance that will be used instead of Mage_Core_Model_Url in navigation mode
     */
    protected function _injectUrlModel($mode)
    {
        switch ($mode) {
            case self::MODE_DESIGN:
                $this->_urlModelFactory->replaceClassName(self::URL_MODEL_DESIGN_MODE_CLASS_NAME);
                break;
            case self::MODE_NAVIGATION:
            default:
                $this->_urlModelFactory->replaceClassName(self::URL_MODEL_NAVIGATION_MODE_CLASS_NAME);
                break;
        }
    }

    /**
     * Replace layout update resource model with custom vde one
     */
    protected function _injectLayoutUpdateResourceModel()
    {
        $this->_objectManager->configure(array(
            'preferences' => array(
                self::LAYOUT_UPDATE_RESOURCE_MODEL_CORE_CLASS_NAME => self::LAYOUT_UPDATE_RESOURCE_MODEL_VDE_CLASS_NAME
            )
        ));
    }

    /**
     * Set current VDE theme
     */
    protected function _setTheme()
    {
        $themeId = $this->_backendSession->getData(self::CURRENT_THEME_SESSION_KEY);
        if ($themeId !== null) {
            $this->_application->getStore()
                ->setConfig(Mage_Core_Model_Design_PackageInterface::XML_PATH_THEME_ID, $themeId);
        }
    }

    /**
     * Disable some cache types in VDE mode
     */
    protected function _disableCache()
    {
        foreach ($this->_dataHelper->getDisabledCacheTypes() as $cacheCode) {
            if ($this->_cacheManager->canUse($cacheCode)) {
                $this->_cacheManager->banUse($cacheCode);
            }
        }
    }
}
