<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency rate import model (From www.webservicex.net)
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Currency_Import_Webservicex extends Mage_Directory_Model_Currency_Import_Abstract
{
    protected $_url = 'http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate?FromCurrency={{CURRENCY_FROM}}&ToCurrency={{CURRENCY_TO}}';
    protected $_messages = array();

     /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;

    public function __construct()
    {
        $this->_httpClient = new Varien_Http_Client();
    }

    protected function _convert($currencyFrom, $currencyTo, $retry=0)
    {
        $url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $this->_url);
        $url = str_replace('{{CURRENCY_TO}}', $currencyTo, $url);

        try {
            $response = $this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => Mage::getStoreConfig('currency/webservicex/timeout')))
                ->request('GET')
                ->getBody();

            $xml = simplexml_load_string($response, null, LIBXML_NOERROR);
            if( !$xml ) {
                $this->_messages[] = Mage::helper('Mage_Directory_Helper_Data')->__('Cannot retrieve rate from %s.', $url);
                return null;
            }
            return (float) $xml;
        }
        catch (Exception $e) {
            if( $retry == 0 ) {
                $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = Mage::helper('Mage_Directory_Helper_Data')->__('Cannot retrieve rate from %s.', $url);
            }
        }
    }
}