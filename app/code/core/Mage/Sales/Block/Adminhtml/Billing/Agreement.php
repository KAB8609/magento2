<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreement grid container
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize billing agreements grid container
     *
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_billing_agreement';
        $this->_blockGroup = 'Mage_Sales';
        $this->_headerText = Mage::helper('Mage_Sales_Helper_Data')->__('Billing Agreements');
        parent::__construct();
        $this->_removeButton('add');
    }
}
