<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create sidebar cart block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Cart
    extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    /**
     * Storage action on selected item
     *
     * @var string
     */
    protected $_sidebarStorageAction = 'add_cart_item';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_cart');
        $this->setDataId('cart');
    }

    public function getHeaderText()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Shopping Cart');
    }

    /**
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        $collection = $this->getData('item_collection');
        if (is_null($collection)) {
            $collection = $this->getCreateOrderModel()->getCustomerCart()->getAllVisibleItems();
            $this->setData('item_collection', $collection);
        }
        return $collection;
    }

    public function canDisplayItemQty()
    {
        return true;
    }

    /**
     * Retrieve identifier of block item
     *
     * @param Varien_Object $item
     * @return int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }

    /**
     * Retrieve product identifier linked with item
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  int
     */
    public function getProductId($item)
    {
        return $item->getProduct()->getId();
    }

    /**
     * Prepare layout
     *
     * Add button that clears customer's shopping cart
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Cart
     */
    protected function _prepareLayout()
    {
        $deleteAllConfirmString = Mage::helper('Mage_Sales_Helper_Data')->__('Are you sure you want to delete all items from shopping cart?');
        $button = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData(array(
            'label' => Mage::helper('Mage_Sales_Helper_Data')->__('Clear Shopping Cart'),
            'onclick' => 'order.clearShoppingCart(\'' . $deleteAllConfirmString . '\')',
            'style' => 'float: right;'
        ));
        $this->setChild('empty_customer_cart_button', $button);

        return parent::_prepareLayout();
    }
}
