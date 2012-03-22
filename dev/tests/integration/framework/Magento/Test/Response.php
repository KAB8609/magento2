<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * HTTP response implementation that is used instead core one for testing
 */
class Magento_Test_Response extends Mage_Core_Controller_Response_Http
{
    /**
     * Prevent generating exceptions if headers are already sent
     *
     * Prevents throwing an exception in Zend_Controller_Response_Abstract::canSendHeaders()
     * All functionality that depend on headers validation should be covered with unit tests by mocking response.
     *
     * @param bool $throw
     * @return bool
     */
    public function canSendHeaders($throw = false)
    {
        return true;
    }

    public function sendResponse()
    {
        Mage::dispatchEvent('http_response_send_before', array('response'=>$this));
    }
}
