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
 * Customer address attributes Grid Container
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Define controller, block and labels
     *
     */
    public function __construct()
    {
        $this->_blockGroup = 'Enterprise_Customer';
        $this->_controller = 'adminhtml_customer_address_attribute';
        $this->_headerText = Mage::helper('Enterprise_Customer_Helper_Data')->__('Manage Customer Address Attributes');
        $this->_addButtonLabel = Mage::helper('Enterprise_Customer_Helper_Data')->__('Add New Attribute');
        parent::__construct();
    }
}
