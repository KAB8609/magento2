<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Shipping Info Model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Shipping_Info extends Magento_Object
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
     * @param string $hash
     * @return Magento_Shipping_Model_Info
     */
    public function loadByHash($hash)
    {
        $data = Mage::helper('Magento_Rma_Helper_Data')->decodeTrackingHash($hash);

        if (!empty($data)) {
            $this->setData($data['key'], $data['id']);
            $this->setProtectCode($data['hash']);

            if ($this->getRmaId()>0) {
                $this->getTrackingInfoByRma();
            } else {
                $this->getTrackingInfoByTrackId();
            }
        }
        return $this;
    }

    /**
     * Generating tracking info
     *
     * @param string $hash
     * @return Magento_Shipping_Model_Info
     */
    public function loadPackage($hash)
    {
        $data = Mage::helper('Magento_Rma_Helper_Data')->decodeTrackingHash($hash);
        $package = array();
        if (!empty($data)) {
            $this->setData($data['key'], $data['id']);
            $this->setProtectCode($data['hash']);
            if ($rma = $this->_initRma()) {
                $package = $rma->getShippingLabel();
            }
        }
        return $package;
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
     * Instantiate RMA model
     *
     * @return Magento_Rma_Model_Rma || false
     */
    protected function _initRma()
    {
        /* @var $model Magento_Rma_Model_Rma */
        $model = Mage::getModel('Magento_Rma_Model_Rma');
        $rma = $model->load($this->getRmaId());
        if (!$rma->getEntityId() || $this->getProtectCode() != $rma->getProtectCode()) {
            return false;
        }
        return $rma;
    }

    /**
     * Retrieve all tracking by RMA id
     *
     * @return array
     */
    public function getTrackingInfoByRma()
    {
        $shipTrack = array();
        $rma = $this->_initRma();
        if ($rma) {
            $increment_id   = $rma->getIncrementId();
            $tracks         = $rma->getTrackingNumbers();
            $trackingInfos  = array();

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
        $track = Mage::getModel('Magento_Rma_Model_Shipping')->load($this->getTrackId());
        if ($track->getId() && $this->getProtectCode() == $track->getProtectCode()) {
            $this->_trackingInfo = array(array($track->getNumberDetail()));
        }
        return $this->_trackingInfo;
    }
}