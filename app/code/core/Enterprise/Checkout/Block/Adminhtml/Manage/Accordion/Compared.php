<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for products in compared list
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Compared
    extends Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'compared';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_compared');
        if ($this->_getStore()) {
            $this->setHeaderText(
                Mage::helper('Enterprise_Checkout_Helper_Data')->__('Products in the Comparison List (%s)', $this->getItemsCount())
            );
        }
    }


    /**
     * Return items collection
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $attributes = Mage::getSingleton('Mage_Catalog_Model_Config')->getProductAttributes();
            $collection = Mage::getModel('Mage_Catalog_Model_Product_Compare_List')
                ->getItemCollection()
                ->useProductItem(true)
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->setCustomerId($this->_getCustomer()->getId())
                ->addAttributeToSelect($attributes)
                ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
            Mage::getSingleton('Mage_CatalogInventory_Model_Stock_Status')->addIsInStockFilterToCollection($collection);
            $collection = Mage::helper('Mage_Adminhtml_Helper_Sales')->applySalableProductTypesFilter($collection);
            $collection->addOptionsToResult();
            $this->setData('items_collection', $collection);
        }
        return $this->_getData('items_collection');
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/viewCompared', array('_current'=>true));
    }
}
