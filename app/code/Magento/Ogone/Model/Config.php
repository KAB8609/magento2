<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config model
 */
class Magento_Ogone_Model_Config extends Magento_Payment_Model_Config
{
    const OGONE_PAYMENT_PATH = 'payment/ogone/';

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Core_Helper_Data $coreData
     */
    public function __construct(
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_Core_Helper_Data $coreData
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_coreData = $coreData;
    }

    /**
     * Return ogone payment config information
     *
     * @param string $path
     * @param int $storeId
     * @return Simple_Xml
     */
    public function getConfigData($path, $storeId=null)
    {
        if (!empty($path)) {
            return $this->_coreStoreConfig->getConfig(self::OGONE_PAYMENT_PATH . $path, $storeId);
        }
        return false;
    }

    /**
     * Return SHA1-in crypt key from config. Setup on admin place.
     *
     * @param int $storeId
     * @return string
     */
    public function getShaInCode($storeId=null)
    {
        return $this->_coreData->decrypt($this->getConfigData('secret_key_in', $storeId));
    }

    /**
     * Return SHA1-out crypt key from config. Setup on admin place.
     * @param int $storeId
     * @return string
     */
    public function getShaOutCode($storeId=null)
    {
        return $this->_coreData->decrypt($this->getConfigData('secret_key_out', $storeId));
    }

    /**
     * Return gateway path, get from confing. Setup on admin place.
     *
     * @param int $storeId
     * @return string
     */
    public function getGatewayPath($storeId=null)
    {
        return $this->getConfigData('ogone_gateway', $storeId);
    }

    /**
     * Get PSPID, affiliation name in ogone system
     *
     * @param int $storeId
     * @return string
     */
    public function getPSPID($storeId=null)
    {
        return $this->getConfigData('pspid', $storeId);
    }

    /**
     * Get paypage template for magento style templates using
     *
     * @return string
     */
    public function getPayPageTemplate()
    {
        return $this->_urlBuilder->getUrl('ogone/api/paypage', array('_nosid' => true));
    }

    /**
     * Return url which ogone system will use as accept
     *
     * @return string
     */
    public function getAcceptUrl()
    {
        return $this->_urlBuilder->getUrl('ogone/api/accept', array('_nosid' => true));
    }

    /**
     * Return url which ogone system will use as decline url
     *
     * @return string
     */
    public function getDeclineUrl()
    {
        return $this->_urlBuilder->getUrl('ogone/api/decline', array('_nosid' => true));
    }

    /**
     * Return url which ogone system will use as exception url
     *
     * @return string
     */
    public function getExceptionUrl()
    {
        return $this->_urlBuilder->getUrl('ogone/api/exception', array('_nosid' => true));
    }

    /**
     * Return url which ogone system will use as cancel url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->_urlBuilder->getUrl('ogone/api/cancel', array('_nosid' => true));
    }

    /**
     * Return url which ogone system will use as our magento home url on ogone success page
     *
     * @return string
     */
    public function getHomeUrl()
    {
        return $this->_urlBuilder->getUrl('checkout/cart', array('_nosid' => true));
    }
}
