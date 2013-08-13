<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme customization config model
 */
class Mage_Theme_Model_Config_Customization
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_design;

    /**
     * @var Magento_Core_Model_Resource_Theme_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Theme customizations which are assigned to store views or as default
     *
     * @see self::_prepareThemeCustomizations()
     * @var array
     */
    protected $_assignedTheme;

    /**
     * Theme customizations which are not assigned to store views or as default
     *
     * @see self::_prepareThemeCustomizations()
     * @var array
     */
    protected $_unassignedTheme;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager,
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $collectionFactory
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_Resource_Theme_CollectionFactory $collectionFactory
    ) {
        $this->_storeManager    = $storeManager;
        $this->_design          = $design;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Return theme customizations which are assigned to store views
     *
     * @see self::_prepareThemeCustomizations()
     * @return array
     */
    public function getAssignedThemeCustomizations()
    {
        if (is_null($this->_assignedTheme)) {
            $this->_prepareThemeCustomizations();
        }
        return $this->_assignedTheme;
    }

    /**
     * Return theme customizations which are not assigned to store views.
     *
     * @see self::_prepareThemeCustomizations()
     * @return array
     */
    public function getUnassignedThemeCustomizations()
    {
        if (is_null($this->_unassignedTheme)) {
            $this->_prepareThemeCustomizations();
        }
        return $this->_unassignedTheme;
    }

    /**
     * Return stores grouped by assigned themes
     *
     * @return array
     */
    public function getStoresByThemes()
    {
        $storesByThemes = array();
        $stores = $this->_storeManager->getStores();
        /** @var $store Magento_Core_Model_Store */
        foreach ($stores as $store) {
            $themeId = $this->_getConfigurationThemeId($store);
            if (!isset($storesByThemes[$themeId])) {
                $storesByThemes[$themeId] = array();
            }
            $storesByThemes[$themeId][] = $store;
        }
        return $storesByThemes;
    }

    /**
     * Check if current theme has assigned to any store
     *
     * @param Magento_Core_Model_Theme $theme
     * @param null|Magento_Core_Model_Store $store
     * @return bool
     */
    public function isThemeAssignedToStore($theme, $store = null)
    {
        if (null === $store) {
            $assignedThemes = $this->getAssignedThemeCustomizations();
            return isset($assignedThemes[$theme->getId()]);
        }
        return  $this->_isThemeAssignedToSpecificStore($theme, $store);
    }

    /**
     * Check if there are any themes assigned
     *
     * @return bool
     */
    public function hasThemeAssigned()
    {
        return count($this->getAssignedThemeCustomizations()) > 0;
    }

    /**
     * Is theme assigned to specific store
     *
     * @param Magento_Core_Model_Theme $theme
     * @param Magento_Core_Model_Store $store
     * @return bool
     */
    protected function _isThemeAssignedToSpecificStore($theme, $store)
    {
        return $theme->getId() == $this->_getConfigurationThemeId($store);
    }

    /**
     * Get configuration theme id
     *
     * @param $store
     * @return int
     */
    protected function _getConfigurationThemeId($store)
    {
        return $this->_design->getConfigurationDesignTheme(
            Magento_Core_Model_App_Area::AREA_FRONTEND,
            array('store' => $store)
        );
    }

    /**
     * Fetch theme customization and sort them out to arrays:
     * self::_assignedTheme and self::_unassignedTheme.
     *
     * NOTE: To get into "assigned" list theme customization not necessary should be assigned to store-view directly.
     * It can be set to website or as default theme and be used by store-view via config fallback mechanism.
     *
     * @return $this
     */
    protected function _prepareThemeCustomizations()
    {
        /** @var Magento_Core_Model_Resource_Theme_Collection $themeCollection */
        $themeCollection = $this->_collectionFactory->create();
        $themeCollection->filterThemeCustomizations();

        $assignedThemes = $this->getStoresByThemes();

        $this->_assignedTheme = array();
        $this->_unassignedTheme = array();

        /** @var $theme Magento_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            if (isset($assignedThemes[$theme->getId()])) {
                $theme->setAssignedStores($assignedThemes[$theme->getId()]);
                $this->_assignedTheme[$theme->getId()] = $theme;
            } else {
                $this->_unassignedTheme[$theme->getId()] = $theme;
            }
        }

        return $this;
    }


}
