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
 * Adminhtml customers list block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Customer extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'customer';
        $this->_headerText = Mage::helper('Magento_Customer_Helper_Data')->__('Customers');
        $this->_addButtonLabel = Mage::helper('Magento_Customer_Helper_Data')->__('Add New Customer');
        parent::_construct();
    }

}
