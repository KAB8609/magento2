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
 * "Add by SKU" error block
 *
 * @method Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Abstract setListType()
 * @method string                                                  getListType()
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Abstract extends Mage_Adminhtml_Block_Widget
{
    /*
     * JS listType of the error grid
     */
    const LIST_TYPE = 'errors';

    /**
     * List of failed items
     *
     * @var null|array
     */
    protected $_failedItems = null;

    /**
     * Cart instance
     *
     * @var null|Enterprise_Checkout_Model_Cart
     */
    protected $_cart;

    /**
     * Define ID
     */
    public function __construct()
    {
        $this->setListType(self::LIST_TYPE);
        $this->setTemplate('sku/errors.phtml');
    }

    /**
     * Accordion header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('<span id="sku-attention-num">%s</span> product(s) require attention', count($this->getFailedItems()));
    }

    /**
     * Retrieve CSS class for header
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'sku-errors';
    }

    /**
     * Retrieve "Add to order" button
     *
     * @return mixed
     */
    public function getButtonsHtml()
    {
        $buttonData = array(
            'label'   => $this->__('Remove All'),
            'onclick' => 'addBySku.removeAllFailed()',
            'class'   => 'delete',
        );
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData($buttonData)->toHtml();
    }

    /**
     * Retrieve items marked as unsuccessful after prepareAddProductsBySku()
     *
     * @return array
     */
    public function getFailedItems()
    {
        if (is_null($this->_failedItems)) {
            $this->_failedItems = $this->getCart()->getFailedItems();
        }
        return $this->_failedItems;
    }

    /**
     * Retrieve url to configure item
     *
     * @return string
     */
    abstract public function getConfigureUrl();

    /**
     * Disable output of error grid in case no errors occurred
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->getFailedItems();
        if (empty($this->_failedItems)) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Implementation-specific JavaScript to be inserted into template
     *
     * @return string
     */
    public function getAdditionalJavascript()
    {
        return '';
    }

    /**
     * Retrieve cart instance
     *
     * @return Enterprise_Checkout_Model_Cart
     */
    public function getCart()
    {
        if (!$this->_cart) {
            $this->_cart =  Mage::getModel('Enterprise_Checkout_Model_Cart');
        }
        return $this->_cart;
    }

    /**
     * Retrieve current store instance
     *
     * @abstract
     * @return Mage_Core_Model_Store
     */
    abstract public function getStore();
}
