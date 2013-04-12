﻿<?php
/**
 * Observer for the Saas_Index module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Model_Observer
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    private $_request;

    /**
     * @var Mage_Core_Controller_Response_Http
     */
    private $_response;

    /**
     * @var Mage_Backend_Model_Url
     */
    private $_modelUrl;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Mage_Backend_Model_Url $modelUrl
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Mage_Backend_Model_Url $modelUrl
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_modelUrl = $modelUrl;
    }

    /**
     * Redefine Magento Index functionality
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Index_Model_Observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function redefineIndex(Varien_Event_Observer $observer)
    {
        if ($this->_request->getControllerModule() == 'Mage_Index_Adminhtml'
            && $this->_request->getControllerName() == 'process'
        ) {
            $this->_forward('list' == $this->_request->getActionName() ? 'index' : 'noroute');
        }
        return $this;
    }

    /**
     * Throw control to another action
     *
     * @param string $action
     */
    protected function _forward($action)
    {
        $this->_request->initForward()
            ->setControllerName('saas_index')
            ->setModuleName('admin')
            ->setActionName($action)
            ->setDispatched(false);
    }
}
