<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer attributes edit page tabs
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Edit_Tabs
    extends Magento_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize edit tabs
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('customer_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Enterprise_Customer_Helper_Data')->__('Attribute Information'));
    }
}
