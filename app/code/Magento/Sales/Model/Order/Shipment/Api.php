<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order shippment API
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Shipment_Api extends Magento_Sales_Model_Api_Resource
{
    public function __construct(Magento_Api_Helper_Data $apiHelper)
    {
        parent::__construct($apiHelper);
        $this->_attributesMap['shipment'] = array('shipment_id' => 'entity_id');

        $this->_attributesMap['shipment_item'] = array('item_id'    => 'entity_id');

        $this->_attributesMap['shipment_comment'] = array('comment_id' => 'entity_id');

        $this->_attributesMap['shipment_track'] = array('track_id'   => 'entity_id');
    }

    /**
     * Retrieve shipments by filters
     *
     * @param null|object|array $filters
     * @return array
     */
    public function items($filters = null)
    {
        $shipments = array();
        //TODO: add full name logic
        $shipmentCollection = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Shipment_Collection')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('total_qty')
            ->addAttributeToSelect('entity_id')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
            ->joinAttribute('order_increment_id', 'order/increment_id', 'order_id', null, 'left')
            ->joinAttribute('order_created_at', 'order/created_at', 'order_id', null, 'left');

        /** @var $apiHelper Magento_Api_Helper_Data */
        $apiHelper = Mage::helper('Magento_Api_Helper_Data');
        try {
            $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['shipment']);
            foreach ($filters as $field => $value) {
                $shipmentCollection->addFieldToFilter($field, $value);
            }
        } catch (Magento_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        foreach ($shipmentCollection as $shipment) {
            $shipments[] = $this->_getAttributes($shipment, 'shipment');
        }

        return $shipments;
    }

    /**
     * Retrieve shipment information
     *
     * @param string $shipmentIncrementId
     * @return array
     */
    public function info($shipmentIncrementId)
    {
        $shipment = Mage::getModel('Magento_Sales_Model_Order_Shipment')->loadByIncrementId($shipmentIncrementId);

        /* @var $shipment Magento_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        $result = $this->_getAttributes($shipment, 'shipment');

        $result['items'] = array();
        foreach ($shipment->getAllItems() as $item) {
            $result['items'][] = $this->_getAttributes($item, 'shipment_item');
        }

        $result['tracks'] = array();
        foreach ($shipment->getAllTracks() as $track) {
            $result['tracks'][] = $this->_getAttributes($track, 'shipment_track');
        }

        $result['comments'] = array();
        foreach ($shipment->getCommentsCollection() as $comment) {
            $result['comments'][] = $this->_getAttributes($comment, 'shipment_comment');
        }

        return $result;
    }

    /**
     * Add tracking number to order
     *
     * @param string $shipmentIncrementId
     * @param string $carrier
     * @param string $title
     * @param string $trackNumber
     * @return int
     */
    public function addTrack($shipmentIncrementId, $carrier, $title, $trackNumber)
    {
        $shipment = Mage::getModel('Magento_Sales_Model_Order_Shipment')->loadByIncrementId($shipmentIncrementId);

        /* @var $shipment Magento_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        $carriers = $this->_getCarriers($shipment);

        if (!isset($carriers[$carrier])) {
            $this->_fault('data_invalid', __('We don\'t recognize the carrier you selected.'));
        }

        $track = Mage::getModel('Magento_Sales_Model_Order_Shipment_Track')
                    ->setNumber($trackNumber)
                    ->setCarrierCode($carrier)
                    ->setTitle($title);

        $shipment->addTrack($track);

        try {
            $shipment->save();
            $track->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $track->getId();
    }

    /**
     * Remove tracking number
     *
     * @param string $shipmentIncrementId
     * @param int $trackId
     * @return boolean
     */
    public function removeTrack($shipmentIncrementId, $trackId)
    {
        $shipment = Mage::getModel('Magento_Sales_Model_Order_Shipment')->loadByIncrementId($shipmentIncrementId);

        /* @var $shipment Magento_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        if(!$track = $shipment->getTrackById($trackId)) {
            $this->_fault('track_not_exists');
        }

        try {
            $track->delete();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('track_not_deleted', $e->getMessage());
        }

        return true;
    }

    /**
     * Send email with shipment data to customer
     *
     * @param string $shipmentIncrementId
     * @param string $comment
     * @return bool
     */
    public function sendInfo($shipmentIncrementId, $comment = '')
    {
        /* @var $shipment Magento_Sales_Model_Order_Shipment */
        $shipment = Mage::getModel('Magento_Sales_Model_Order_Shipment')->loadByIncrementId($shipmentIncrementId);

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $shipment->sendEmail(true, $comment)
                ->setEmailSent(true)
                ->save();
            $historyItem = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Status_History_Collection')
                ->getUnnotifiedForInstance($shipment, Magento_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME);
            if ($historyItem) {
                $historyItem->setIsCustomerNotified(1);
                $historyItem->save();
            }
        } catch (Magento_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Add comment to shipment
     *
     * @param string $shipmentIncrementId
     * @param string $comment
     * @param boolean $email
     * @param boolean $includeInEmail
     * @return boolean
     */
    public function addComment($shipmentIncrementId, $comment, $email = false, $includeInEmail = false)
    {
        $shipment = Mage::getModel('Magento_Sales_Model_Order_Shipment')->loadByIncrementId($shipmentIncrementId);

        /* @var $shipment Magento_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }


        try {
            $shipment->addComment($comment, $email);
            $shipment->sendUpdateEmail($email, ($includeInEmail ? $comment : ''));
            $shipment->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Retrieve allowed shipping carriers for specified order
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function getCarriers($orderIncrementId)
    {
        $order = Mage::getModel('Magento_Sales_Model_Order')->loadByIncrementId($orderIncrementId);

        /**
          * Check order existing
          */
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }

        return $this->_getCarriers($order);
    }

    /**
     * Retrieve shipping carriers for specified order
     *
     * @param Magento_Eav_Model_Entity_Abstract $object
     * @return array
     */
    protected function _getCarriers($object)
    {
        $carriers = array();
        $carrierInstances = Mage::getSingleton('Magento_Shipping_Model_Config')->getAllCarriers(
            $object->getStoreId()
        );

        $carriers['custom'] = __('Custom Value');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }

        return $carriers;
    }

} // Class Magento_Sales_Model_Order_Shipment_Api End