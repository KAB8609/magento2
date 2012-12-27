<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract redirect/forward action class
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Controller_Varien_ActionAbstract implements Mage_Core_Controller_Varien_DispatchableInterface
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Mage_Core_Controller_Response_Http
     */
    protected $_response;

    /**
     * @var string
     */
    protected $_currentArea;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param string $areaCode
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        $areaCode = null
    ) {
        $this->_request     = $request;
        $this->_response    = $response;
        $this->_currentArea = $areaCode;
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve response object
     *
     * @return Mage_Core_Controller_Response_Http
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Retrieve full bane of current action current controller and
     * current module
     *
     * @param   string $delimiter
     * @return  string
     */
    public function getFullActionName($delimiter = '_')
    {
        return $this->getRequest()->getRequestedRouteName() . $delimiter .
            $this->getRequest()->getRequestedControllerName() . $delimiter .
            $this->getRequest()->getRequestedActionName();
    }
}
