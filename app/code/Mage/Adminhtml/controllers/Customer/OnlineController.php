<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Customer_OnlineController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->_title($this->__('Online Customers'));

        if($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        $this->_setActiveMenu('Mage_Customer::customer_online');

        $this->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Customer_Online', 'customers'));

        $this->_addBreadcrumb(Mage::helper('Mage_Customer_Helper_Data')->__('Customers'), Mage::helper('Mage_Customer_Helper_Data')->__('Customers'));
        $this->_addBreadcrumb(Mage::helper('Mage_Customer_Helper_Data')->__('Online Customers'), Mage::helper('Mage_Customer_Helper_Data')->__('Online Customers'));

        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Customer::online');
    }
}
