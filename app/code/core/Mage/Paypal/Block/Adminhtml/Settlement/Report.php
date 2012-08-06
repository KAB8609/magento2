<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml paypal settlement reports grid block
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Adminhtml_Settlement_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Prepare grid container, add additional buttons
     */
    public function __construct()
    {
        $this->_blockGroup = 'Mage_Paypal';
        $this->_controller = 'adminhtml_settlement_report';
        $this->_headerText = Mage::helper('Mage_Paypal_Helper_Data')->__('PayPal Settlement Reports');
        parent::__construct();
        $this->_removeButton('add');
        $message = Mage::helper('Mage_Paypal_Helper_Data')->__('Connecting to PayPal SFTP server to fetch new reports. Are you sure you want to proceed?');
        if (true == Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('Mage_Paypal::fetch')) {
            $this->_addButton('fetch', array(
                'label'   => Mage::helper('Mage_Paypal_Helper_Data')->__('Fetch Updates'),
                'onclick' => "confirmSetLocation('{$message}', '{$this->getUrl('*/*/fetch')}')",
                'class'   => 'task'
            ));
        }
    }
}
