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
 * Adminhtml sales order create items block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Contains button descriptions to be shown at the top of accordion
     * @var array
     */
    protected $_buttons = array();

    /**
     * Define block ID
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_items');
        $this->_buttons[] = array(
            'label' => Mage::helper('sales')->__('Add Products'),
            'onclick' => "order.productGridShow(this)",
            'class' => 'add');
    }

    /**
     * Accordion header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Items Ordered');
    }

    /**
     * Returns all visible items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Add button to the items header
     *
     * @param $args array
     */
    public function addButton($args)
    {
        $this->_buttons[] = $args;
    }

    /**
     * Render buttons and return HTML code
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $html = '';
        // Make buttons to be rendered in opposite order of addition. This makes "Add products" the last one.
        $this->_buttons = array_reverse($this->_buttons);
        foreach ($this->_buttons as $buttonData) {
            $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData($buttonData)->toHtml();
        }

        return $html;
    }

    /**
     * Return HTML code of the block
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getStoreId()) {
            return parent::_toHtml();
        }
        return '';
    }

}
