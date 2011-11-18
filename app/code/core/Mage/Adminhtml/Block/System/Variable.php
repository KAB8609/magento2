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
 * Custom Varieble Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Variable extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_controller = 'system_variable';
        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Custom Variables');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add New Variable'));
    }
}
