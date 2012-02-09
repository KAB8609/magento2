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
 * Block with description of why item has not been added to ordered items list
 *
 * @method Varien_Object                                                   getItem()
 * @method Mage_Catalog_Model_Product                                      getProduct()
 * @method Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Description setItem()
 * @method Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Description setProduct()
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Description extends Mage_Adminhtml_Block_Template
{
    /**
     * Define template
     */
    public function __construct()
    {
        $this->setTemplate('sku/errors/grid/description.phtml');
    }

    /**
     * Retrieves HTML code of "Configure" button
     *
     * @return string
     */
    public function getConfigureButtonHtml()
    {
        $canConfigure = $this->getProduct()->canConfigure();
        /* @var $button Mage_Adminhtml_Block_Widget_Button */
        $button = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button', '', array(
            'class'    => $canConfigure ? '' : 'disabled',
            'onclick'  => $canConfigure ? "addBySku.configure({$this->getProduct()->getId()})" : '',
            'disabled' => !$canConfigure,
            'label'    => Mage::helper('Enterprise_Checkout_Helper_Data')->__('Configure'),
            'type'     => 'button',
        ));

        return $button->toHtml();
    }

    /**
     * Retrieve HTML name for element
     *
     * @return string
     */
    public function getSourceId()
    {
        return $this->_prepareLayout()->getLayout()->getBlock('sku_error_grid')->getId();
    }

    /**
     * Returns error message of the item
     * @see Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_* constants for $code
     *
     * @param Varien_Object $item
     * @return string
     */
    public function getErrorMessage($item)
    {
        return Mage::helper('Enterprise_Checkout_Helper_Data')->getMessageByItem($item);
    }
}
