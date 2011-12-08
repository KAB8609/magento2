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
 * Adminhtml customers list block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'customer';
        $this->_headerText = Mage::helper('Mage_Customer_Helper_Data')->__('Manage Customers');
        $this->_addButtonLabel = Mage::helper('Mage_Customer_Helper_Data')->__('Add New Customer');
        parent::__construct();
    }

}
