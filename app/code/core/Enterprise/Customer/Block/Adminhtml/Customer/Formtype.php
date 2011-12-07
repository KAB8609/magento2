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
 * Form Types Grid Container Block
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Formtype extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     *
     */
    public function __construct()
    {
        $this->_blockGroup = 'Enterprise_Customer';
        $this->_controller = 'adminhtml_customer_formtype';
        $this->_headerText = Mage::helper('Enterprise_Customer_Helper_Data')->__('Manage Form Types');

        parent::__construct();

        $this->_updateButton('add', 'label', Mage::helper('Enterprise_Customer_Helper_Data')->__('New Form Type'));
    }
}
