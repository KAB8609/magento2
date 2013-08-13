<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders controller
 *
 * @category   Mage
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Shipping_Controller_Tracking extends Magento_Core_Controller_Front_Action
{
    /**
     * Ajax action
     *
     */
    public function ajaxAction()
    {
        if ($order = $this->_initOrder()) {
            $response = '';
            $tracks = $order->getTracksCollection();

            $block = $this->_objectManager->create('Magento_Core_Block_Template');
            $block->setType('Magento_Core_Block_Template')
                ->setTemplate('order/trackinginfo.phtml');

            foreach ($tracks as $track){
                $trackingInfo = $track->getNumberDetail();
                $block->setTrackingInfo($trackingInfo);
                $response .= $block->toHtml()."\n<br />";
            }

            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Popup action
     * Shows tracking info if it's present, otherwise redirects to 404
     */
    public function popupAction()
    {
        $shippingInfoModel = Mage::getModel('Mage_Shipping_Model_Info')->loadByHash($this->getRequest()->getParam('hash'));
        Mage::register('current_shipping_info', $shippingInfoModel);
        if (count($shippingInfoModel->getTrackingInfo()) == 0) {
            $this->norouteAction();
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }


    /**
     * Initialize order model instance
     *
     * @return Magento_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');

        $order = Mage::getModel('Magento_Sales_Model_Order')->load($id);
        $customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId();

        if (!$order->getId() || !$customerId || $order->getCustomerId() != $customerId) {
            return false;
        }
        return $order;
    }

}
