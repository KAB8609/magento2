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
 * Shipment tracking control form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Shipment_View_Tracking extends Magento_Adminhtml_Block_Template
{
    /**
     * Prepares layout of block
     *
     * @return Magento_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('shipment_tracking_info').parentNode, '".$this->getSubmitUrl()."')";
        $this->addChild('save_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'   => Mage::helper('Magento_Sales_Helper_Data')->__('Add'),
            'class'   => 'save',
            'onclick' => $onclick
        ));
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * Retrieve save url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addTrack/', array('shipment_id'=>$this->getShipment()->getId()));
    }

    /**
     * Retrive save button html
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve remove url
     *
     * @return string
     */
    public function getRemoveUrl($track)
    {
        return $this->getUrl('*/*/removeTrack/', array(
            'shipment_id' => $this->getShipment()->getId(),
            'track_id' => $track->getId()
        ));
    }

    /**
     * Retrieve remove url
     *
     * @return string
     */
    public function getTrackInfoUrl($track)
    {
        return $this->getUrl('*/*/viewTrack/', array(
            'shipment_id' => $this->getShipment()->getId(),
            'track_id' => $track->getId()
        ));
    }

    /**
     * Retrieve
     *
     * @return unknown
     */
    public function getCarriers()
    {
        $carriers = array();
        $carrierInstances = Mage::getSingleton('Mage_Shipping_Model_Config')->getAllCarriers(
            $this->getShipment()->getStoreId()
        );
        $carriers['custom'] = Mage::helper('Magento_Sales_Helper_Data')->__('Custom Value');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        return $carriers;
    }

    public function getCarrierTitle($code)
    {
        if ($carrier = Mage::getSingleton('Mage_Shipping_Model_Config')->getCarrierInstance($code)) {
            return $carrier->getConfigData('title');
        }
        else {
            return Mage::helper('Magento_Sales_Helper_Data')->__('Custom Value');
        }
        return false;
    }
}
