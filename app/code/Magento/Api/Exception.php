<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Api exception
 *
 * @category   Mage
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Exception extends Magento_Core_Exception
{
    protected $_customMessage = null;

    public function __construct($faultCode, $customMessage = null)
    {
        parent::__construct($faultCode);
        $this->_customMessage = $customMessage;
    }

    /**
     * Custom error message, if error is not in api.
     *
     * @return unknown
     */
    public function getCustomMessage()
    {
        return $this->_customMessage;
    }
} // Class Magento_Api_Model_Resource_Exception End
