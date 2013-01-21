<?php
/**
 * Test Json Renderer for REST.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Response_Rest_Renderer_JsonTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Response_Rest_Renderer_Json */
    protected $_restJsonRenderer;

    /** @var Mage_Core_Helper_Data */
    protected $_helperMock;

    protected function setUp()
    {
        /** Prepare mocks and objects for SUT constructor. */
        $this->_helperMock = $this->getMockBuilder('Mage_Core_Helper_Data')->disableOriginalConstructor()->getMock();
        $helperFactoryMock = $this->getMock('Mage_Core_Model_Factory_Helper');
        $helperFactoryMock->expects($this->any())->method('get')->will($this->returnValue($this->_helperMock));
        /** Initialize SUT. */
        $this->_restJsonRenderer = new Mage_Webapi_Controller_Response_Rest_Renderer_Json($helperFactoryMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_helperMock);
        unset($this->_restJsonRenderer);
        parent::tearDown();
    }

    /**
     * Test render method
     */
    public function testRender()
    {
        $arrayToRender = array('key' => 'value');
        /** Assert jsonEncode method in mocked helper will run once */
        $this->_helperMock->expects($this->once())->method('jsonEncode');
        $this->_restJsonRenderer->render($arrayToRender);
    }

    /**
     * Test GetMimeType method
     */
    public function testGetMimeType()
    {
        $expectedMimeType = 'application/json';
        $this->assertEquals($expectedMimeType, $this->_restJsonRenderer->getMimeType(), 'Unexpected mime type.');
    }
}
