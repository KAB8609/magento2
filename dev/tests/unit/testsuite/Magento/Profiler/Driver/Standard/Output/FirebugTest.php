<?php
/**
 * Test class for Magento_Profiler_Driver_Standard_Output_Firebug
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Output_FirebugTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Profiler_Driver_Standard_Output_Firebug
     */
    protected $_output;

    protected function setUp()
    {
        $this->_output = new Magento_Profiler_Driver_Standard_Output_Firebug();
    }

    public function testGetAndSetRequest()
    {
        $this->assertInstanceOf('Zend_Controller_Request_Abstract', $this->_output->getRequest());
        $request = $this->getMock('Zend_Controller_Request_Abstract');
        $this->_output->setRequest($request);
        $this->assertSame($request, $this->_output->getRequest());
    }

    public function testGetAndSetResponse()
    {
        $this->assertInstanceOf('Zend_Controller_Response_Abstract', $this->_output->getResponse());
        $response = $this->getMock('Zend_Controller_Response_Abstract');
        $this->_output->setResponse($response);
        $this->assertSame($response, $this->_output->getResponse());
    }
}
