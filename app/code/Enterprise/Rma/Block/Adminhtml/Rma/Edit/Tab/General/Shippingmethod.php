<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipping Method Block at RMA page
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shippingmethod
    extends Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Abstract
{

    /**
     * PSL Button statuses
     */
    const PSL_DISALLOWED    = 0;
    const PSL_ALLOWED       = 1;
    const PSL_DISABLED      = 2;

    /**
     * Variable to store RMA instance
     *
     * @var null|Enterprise_Rma_Model_Rma
     */
    protected $_rma = null;

    public function _construct()
    {
        $buttonStatus       = Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shippingmethod::PSL_DISALLOWED;
        if ($this->_getShippingAvailability() && $this->getRma() && $this->getRma()->isAvailableForPrintLabel()) {
            $buttonStatus   = Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shippingmethod::PSL_ALLOWED;
        } elseif($this->getRma() && $this->getRma()->getButtonDisabledStatus()) {
            $buttonStatus   = Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shippingmethod::PSL_DISABLED;
        }

        $this->setIsPsl($buttonStatus);
    }

    /**
     * Declare rma instance
     *
     * @return  Enterprise_Rma_Model_Item
     */
    public function getRma()
    {
        if (is_null($this->_rma)) {
            $this->_rma = Mage::registry('current_rma');
        }
        return $this->_rma;
    }

    /**
     * Defines whether Shipping method settings allow to create shipping label
     *
     * @return bool
     */
    protected function _getShippingAvailability()
    {
        $carriers = array();
        if ($this->getRma()) {
            $carriers = Mage::helper('Enterprise_Rma_Helper_Data')->getAllowedShippingCarriers($this->getRma()->getStoreId());
        }
        return !empty($carriers);
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Enterprise_Rma_Model_Shipping
     */
    public function getShipment()
    {
        return Mage::getModel('Enterprise_Rma_Model_Shipping')
            ->getShippingLabelByRma($this->getRma());
    }

    /**
     * Return price according to store
     *
     * @param  string $price
     * @return double
     */
    public function getShippingPrice($price)
    {
        return Mage::app()
            ->getStore($this->getRma()->getStoreId())
            ->convertPrice(
                Mage::helper('Mage_Tax_Helper_Data')->getShippingPrice(
                    $price
                ),
                true,
                false
            )
        ;
    }

    /**
     * Get packed products in packages
     *
     * @return array
     */
    public function getPackages()
    {
        $packages = $this->getShipment()->getPackages();
        if ($packages) {
            $packages = unserialize($packages);
        } else {
            $packages = array();
        }
        return $packages;
    }

    /**
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $storeId    = $this->getRma()->getStoreId();
        $order      = $this->getRma()->getOrder();
        $carrierCode= $this->getShipment()->getCarrierCode();
        if (!$carrierCode) {
            return false;
        }
        $address    = $order->getShippingAddress();
        $shipperAddressCountryCode  = $address->getCountryId();
        $recipientAddressCountryCode= Mage::helper('Enterprise_Rma_Helper_Data')->getReturnAddressModel($storeId)->getCountryId();

        if (($carrierCode == 'fedex' || $carrierCode == 'dhl')
            && $shipperAddressCountryCode != $recipientAddressCountryCode) {
            return true;
        }
        return false;
    }

    /**
     * Get print label button html
     *
     * @return string
     */
    public function getPrintLabelButton()
    {
        $data['id'] = $this->getRma()->getId();
        $url        = $this->getUrl('*/rma/printLabel', $data);

        return $this->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => __('Print Shipping Label'),
                'onclick' => 'setLocation(\'' . $url . '\')'
            ))
            ->toHtml();
    }

    /**
     * Show packages button html
     *
     * @return string
     */
    public function getShowPackagesButton()
    {
        return $this->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => __('Show Packages'),
                'onclick' => 'showPackedWindow();'
            ))
            ->toHtml();
    }

    /**
     * Print button for creating pdf
     *
     * @return string
     */
    public function getPrintButton()
    {
        $data['id'] = $this->getRma()->getId();
        $url        = $this->getUrl('*/rma/printPackage', $data);

        return $this->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => __('Print'),
                'onclick' => 'setLocation(\'' . $url . '\')'
            ))
            ->toHtml();
    }

    /**
     * Return name of container type by its code
     *
     * @param string $code
     * @return string
     */
    public function getContainerTypeByCode($code)
    {
        $carrierCode= $this->getShipment()->getCarrierCode();
        $carrier    = Mage::helper('Enterprise_Rma_Helper_Data')->getCarrier($carrierCode, $this->getRma()->getStoreId());
        if ($carrier) {
            $containerTypes = $carrier->getContainerTypes();
            $containerType = !empty($containerTypes[$code]) ? $containerTypes[$code] : '';
            return $containerType;
        }
        return '';
    }

    /**
     * Return name of delivery confirmation type by its code
     *
     * @param string $code
     * @return string
     */
    public function getDeliveryConfirmationTypeByCode($code)
    {
        $storeId    = $this->getRma()->getStoreId();
        $countryId  = Mage::helper('Enterprise_Rma_Helper_Data')->getReturnAddressModel($storeId)->getCountryId();
        $carrierCode= $this->getShipment()->getCarrierCode();
        $carrier    = Mage::helper('Enterprise_Rma_Helper_Data')->getCarrier($carrierCode, $this->getRma()->getStoreId());
        if ($carrier) {
            $params = new Magento_Object(array('country_recipient' => $countryId));
            $confirmationTypes = $carrier->getDeliveryConfirmationTypes($params);
            $containerType = !empty($confirmationTypes[$code]) ? $confirmationTypes[$code] : '';
            return $containerType;
        }
        return '';
    }

    /**
     * Display formatted price
     *
     * @param float $price
     * @return string
     */
    public function displayPrice($price)
    {
        return $this->getRma()->getOrder()->formatPriceTxt($price);
    }

    /**
     * Get ordered qty of item
     *
     * @param int $itemId
     * @return int|null
     */
    public function getQtyOrderedItem($itemId)
    {
        if ($itemId) {
            return $this->getRma()->getOrder()->getItemById($itemId)->getQtyOrdered()*1;
        } else {
            return;
        }
    }

    /**
     * Return content types of package
     *
     * @return array
     */
    public function getContentTypes()
    {
        $order      = $this->getRma()->getOrder();
        $storeId    = $this->getRma()->getStoreId();
        $address    = $order->getShippingAddress();

        $carrierCode= $this->getShipment()->getCarrierCode();
        $carrier    = Mage::helper('Enterprise_Rma_Helper_Data')->getCarrier($carrierCode, $storeId);

        $countryShipper = Mage::getStoreConfig(Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID, $storeId);
        if ($carrier) {
            $params = new Magento_Object(array(
                'method'            => $carrier->getMethod(),
                'country_shipper'   => $countryShipper,
                'country_recipient' => $address->getCountryId(),
            ));
            return $carrier->getContentTypes($params);
        }
        return array();
    }

    /**
     * Return name of content type by its code
     *
     * @param string $code
     * @return string
     */
    public function getContentTypeByCode($code)
    {
        $contentTypes = $this->getContentTypes();
        if (!empty($contentTypes[$code])) {
            return $contentTypes[$code];
        }
        return '';
    }
}