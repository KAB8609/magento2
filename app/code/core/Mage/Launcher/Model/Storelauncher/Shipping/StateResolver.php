<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * State resolver for Shipping Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Shipping_StateResolver
    extends Mage_Launcher_Model_Tile_ConfigBased_StateResolverAbstract
{
    /**
     * Flag that shows if configuration check is required to identify tile state
     *
     * @var bool
     */
    protected $_isConfigRequired;

    /**
     * @param Mage_Core_Model_App $app
     * @param Mage_Core_Controller_Request_Http $request
     */
    function __construct(
        Mage_Core_Model_App $app,
        Mage_Core_Controller_Request_Http $request
    ) {
        parent::__construct($app);
        // shipping tile can be considered complete when user simply deselects 'Shipping Enabled' checkbox
        $isShippingEnabled = $request->getPost('shipping_enabled');
        $this->_isConfigRequired = !empty($isShippingEnabled);
    }

    /**
     * Resolve state
     *
     * @return bool
     */
    public function isTileComplete()
    {
        if (!$this->_isConfigRequired) {
            return true;
        }

        $shippingConfigPaths = array(
            'carriers_flatrate' => 'carriers/flatrate/active',
            'carriers_ups' => 'carriers/ups/active',
            'carriers_usps' => 'carriers/usps/active',
            'carriers_fedex' => 'carriers/fedex/active',
            'carriers_dhlint' => 'carriers/dhlint/active',
        );
        $currentStore = $this->_app->getStore();
        // the tile is considered to be complete if at least one of the related shipping methods is active
        foreach ($shippingConfigPaths as $configPath) {
            if ((bool)$currentStore->getConfig($configPath)) {
                return true;
            }
        }
        return false;
    }
}
