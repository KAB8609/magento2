<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product inventory data
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory extends Magento_Adminhtml_Block_Widget
{
    protected $_template = 'catalog/product/tab/inventory.phtml';

    public function getBackordersOption()
    {
        if (Mage::helper('Magento_Catalog_Helper_Data')->isModuleEnabled('Magento_CatalogInventory')) {
            return Mage::getSingleton('Magento_CatalogInventory_Model_Source_Backorders')->toOptionArray();
        }

        return array();
    }

    /**
     * Retrieve stock option array
     *
     * @return array
     */
    public function getStockOption()
    {
        if (Mage::helper('Magento_Catalog_Helper_Data')->isModuleEnabled('Magento_CatalogInventory')) {
            return Mage::getSingleton('Magento_CatalogInventory_Model_Source_Stock')->toOptionArray();
        }

        return array();
    }

    /**
     * Return current product instance
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Retrieve Catalog Inventory  Stock Item Model
     *
     * @return Magento_CatalogInventory_Model_Stock_Item
     */
    public function getStockItem()
    {
        return $this->getProduct()->getStockItem();
    }

    public function isConfigurable()
    {
        return $this->getProduct()->isConfigurable();
    }

    public function getFieldValue($field)
    {
        if ($this->getStockItem()) {
            return $this->getStockItem()->getDataUsingMethod($field);
        }

        return Mage::getStoreConfig(Magento_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }

    public function getConfigFieldValue($field)
    {
        if ($this->getStockItem()) {
            if ($this->getStockItem()->getData('use_config_' . $field) == 0) {
                return $this->getStockItem()->getData($field);
            }
        }

        return Mage::getStoreConfig(Magento_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }

    public function getDefaultConfigValue($field)
    {
        return Mage::getStoreConfig(Magento_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }

    /**
     * Is readonly stock
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getInventoryReadonly();
    }

    public function isNew()
    {
        if ($this->getProduct()->getId()) {
            return false;
        }
        return true;
    }

    public function getFieldSuffix()
    {
        return 'product';
    }

    /**
     * Check Whether product type can have fractional quantity or not
     *
     * @return bool
     */
    public function canUseQtyDecimals()
    {
        return $this->getProduct()->getTypeInstance()->canUseQtyDecimals();
    }

    /**
     * Check if product type is virtual
     *
     * @return boolean
     */
    public function isVirtual()
    {
        return $this->getProduct()->getIsVirtual();
    }
}