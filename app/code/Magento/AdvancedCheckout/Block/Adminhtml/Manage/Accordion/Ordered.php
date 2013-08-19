<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for recently ordered products
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Ordered
    extends Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Collection field name for using in controls
     * @var string
     */
    protected $_controlFieldName = 'item_id';

    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'ordered';

    /**
     * Url to configure this grid's items
     */
    protected $_configureRoute = '*/checkout/configureOrderedItem';

    /**
     * Initialize Grid
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_ordered');
        if ($this->_getStore()) {
            $this->setHeaderText(
                Mage::helper('Magento_AdvancedCheckout_Helper_Data')->__('Last ordered items (%s)', $this->getItemsCount())
            );
        }
    }

    /**
     * Returns custom last ordered products renderer for price column content
     *
     * @return null|string
     */
    protected function _getPriceRenderer()
    {
        return 'Magento_AdvancedCheckout_Block_Adminhtml_Manage_Grid_Renderer_Ordered_Price';
    }

    /**
     * Prepare customer wishlist product collection
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $productIds = array();
            $storeIds = $this->_getStore()->getWebsite()->getStoreIds();

            // Load last order of a customer
            /* @var $collection Magento_Core_Model_Resource_Db_Collection_Abstract */
            $collection = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Collection')
                ->addAttributeToFilter('customer_id', $this->_getCustomer()->getId())
                ->addAttributeToFilter('store_id', array('in' => $storeIds))
                ->addAttributeToSort('created_at', 'desc')
                ->setPage(1, 1)
                ->load();
            foreach ($collection as $order) {
                break;
            }

            // Add products to order items
            if (isset($order)) {
                $productIds = array();
                $collection = $order->getItemsCollection();
                foreach ($collection as $item) {
                    if ($item->getParentItem()) {
                        $collection->removeItemByKey($item->getId());
                    } else {
                        $productIds[$item->getProductId()] = $item->getProductId();
                    }
                }
                if ($productIds) {
                    // Load products collection
                    $attributes = Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes();
                    $products = Mage::getModel('Magento_Catalog_Model_Product')->getCollection()
                        ->setStore($this->_getStore())
                        ->addAttributeToSelect($attributes)
                        ->addAttributeToSelect('sku')
                        ->addAttributeToFilter('type_id',
                            array_keys(
                                Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')
                                    ->asArray()
                            )
                        )->addAttributeToFilter('status', Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
                        ->addStoreFilter($this->_getStore())
                        ->addIdFilter($productIds);
                    Mage::getSingleton('Magento_CatalogInventory_Model_Stock_Status')->addIsInStockFilterToCollection($products);
                    $products->addOptionsToResult();

                    // Set products to items
                    foreach ($collection as $item) {
                        $productId = $item->getProductId();
                        $product = $products->getItemById($productId);
                        if ($product) {
                            $item->setProduct($product);
                        } else {
                            $collection->removeItemByKey($item->getId());
                        }
                    }
                }
            }
            $this->setData('items_collection', $productIds ? $collection : parent::getItemsCollection());
        }
        return $this->getData('items_collection');
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/viewOrdered', array('_current'=>true));
    }
}
