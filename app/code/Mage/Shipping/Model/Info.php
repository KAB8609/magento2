<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Shipping_Model_Info extends Magento_Object
{
    /**
     * Tracking info
     *
     * @var array
     */
    protected $_trackingInfo = array();

    /**
     * Generating tracking info
     *
     * @param array $hash
     * @return Mage_Shipping_Model_Info
     */
    public function loadByHash($hash)
    {
        /* @var $helper Mage_Shipping_Helper_Data */
        $helper = Mage::helper('Mage_Shipping_Helper_Data');
        $data = $helper->decodeTrackingHash($hash);
        if (!empty($data)) {
            $this->setData($data['key'], $data['id']);
            $this->setProtectCode($data['hash']);

            if ($this->getOrderId() > 0) {
                $this->getTrackingInfoByOrder();
            } elseif($this->getShipId() > 0) {
                $this->getTrackingInfoByShip();
            } else {
                $this->getTrackingInfoByTrackId();
            }
        }
        return $this;
    }

    /**
     * Retrieve tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        return $this->_trackingInfo;
    }

    /**
     * Instantiate order model
     *
     * @return Mage_Sales_Model_Order|bool
     */
    protected function _initOrder()
    {
        $order = Mage::getModel('Mage_Sales_Model_Order')->load($this->getOrderId());

        if (!$order->getId() || $this->getProtectCode() != $order->getProtectCode()) {
            return false;
        }

        return $order;
    }

    /**
     * Instantiate ship model
     *
     * @return Mage_Sales_Model_Order_Shipment|bool
     */
    protected function _initShipment()
    {
        /* @var $model Mage_Sales_Model_Order_Shipment */
        $model = Mage::getModel('Mage_Sales_Model_Order_Shipment');
        $ship = $model->load($this->getShipId());
        if (!$ship->getEntityId() || $this->getProtectCode() != $ship->getProtectCode()) {
            return false;
        }

        return $ship;
    }

    /**
     * Retrieve all tracking by order id
     *
     * @return array
     */
    public function getTrackingInfoByOrder()
    {
        $shipTrack = array();
        $order = $this->_initOrder();
        if ($order) {
            $shipments = $order->getShipmentsCollection();
            foreach ($shipments as $shipment){
                $increment_id = $shipment->getIncrementId();
                $tracks = $shipment->getTracksCollection();

                $trackingInfos=array();
                foreach ($tracks as $track){
                    $trackingInfos[] = $track->getNumberDetail();
                }
                $shipTrack[$increment_id] = $trackingInfos;
            }
        }
        $this->_trackingInfo = $shipTrack;
        return $this->_trackingInfo;
    }

    /**
     * Retrieve all tracking by ship id
     *
     * @return array
     */
    public function getTrackingInfoByShip()
    {
        $shipTrack = array();
        $shipment = $this->_initShipment();
        if ($shipment) {
            $increment_id = $shipment->getIncrementId();
            $tracks = $shipment->getTracksCollection();

            $trackingInfos=array();
            foreach ($tracks as $track){
                $trackingInfos[] = $track->getNumberDetail();
            }
            $shipTrack[$increment_id] = $trackingInfos;

        }
        $this->_trackingInfo = $shipTrack;
        return $this->_trackingInfo;
    }

    /**
     * Retrieve tracking by tracking entity id
     *
     * @return array
     */
    public function getTrackingInfoByTrackId()
    {
        $track = Mage::getModel('Mage_Sales_Model_Order_Shipment_Track')->load($this->getTrackId());
        if ($track->getId() && $this->getProtectCode() == $track->getProtectCode()) {
            $this->_trackingInfo = array(array($track->getNumberDetail()));
        }
        return $this->_trackingInfo;
    }
}
