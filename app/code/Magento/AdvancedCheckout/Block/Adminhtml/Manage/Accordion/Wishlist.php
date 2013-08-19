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
 * Accordion grid for products in wishlist
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Wishlist
    extends Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Collection field name for using in controls
     * @var string
     */
    protected $_controlFieldName = 'wishlist_item_id';

    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'wishlist';

    /**
     * Url to configure this grid's items
     */
    protected $_configureRoute = '*/checkout/configureWishlistItem';

    /**
     * Initialize Grid
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_wishlist');
        $this->setDefaultSort('added_at');
        $this->setData('open', true);
        if ($this->_getStore()) {
            $this->setHeaderText(
                Mage::helper('Magento_AdvancedCheckout_Helper_Data')->__('Wish List (%s)', $this->getItemsCount())
            );
        }
    }

    /**
     * Return custom object name for js grid
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return 'wishlistItemsGrid';
    }

    /**
     * Create wishlist item collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createItemsCollection()
    {
        return Mage::getModel('Magento_Wishlist_Model_Item')->getCollection();
    }

    /**
     * Return items collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $collection = $this->_createItemsCollection()
                ->addCustomerIdFilter($this->_getCustomer()->getId())
                ->addStoreFilter($this->_getStore()->getWebsite()->getStoreIds())
                ->setVisibilityFilter()
                ->setSalableFilter()
                ->resetSortOrder();

            foreach ($collection as $item) {
                $product = $item->getProduct();
                if ($product) {
                    if (!$product->getStockItem()->getIsInStock() || !$product->isInStock()) {
                        // Remove disabled and out of stock products from the grid
                        $collection->removeItemByKey($item->getId());
                    } else {
                        $item->setName($product->getName());
                        $item->setPrice($product->getPrice());
                    }
                }

            }
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
        return $this->getUrl('*/*/viewWishlist', array('_current'=>true));
    }

    /**
     * Add columns with controls to manage added products and their quantity
     * Uses inherited methods, but modifies Qty column to change renderer
     *
     * @return Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Wishlist
     */
    protected function _addControlColumns()
    {
        parent::_addControlColumns();
        $this->getColumn('qty')->addData(array(
            'renderer' => 'Magento_AdvancedCheckout_Block_Adminhtml_Manage_Grid_Renderer_Wishlist_Qty'
        ));

        return $this;
    }
}
