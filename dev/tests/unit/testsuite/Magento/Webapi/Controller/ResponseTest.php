<?php
/**
 * Test for Webapi Response model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_ResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * Response object.
     *
     * @var \Magento\Webapi\Controller\Response
     */
    protected $_response;

    protected function setUp()
    {
        /** Initialize SUT. */
        $this->_response = new \Magento\Webapi\Controller\Response();
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_response);
        parent::tearDown();
    }

    /**
     * Test addMessage, hasMessage, getMessage, and clearMessages methods.
     */
    public function testMessagesCrud()
    {
        /** Test that new object does not contain any messages. */
        $this->assertFalse($this->_response->hasMessages(), 'New object contains messages.');

        /** Test message adding functionality. */
        $this->_response->addMessage(
            'Message text',
            200,
            array('key' => 'value'),
            \Magento\Webapi\Controller\Response::MESSAGE_TYPE_SUCCESS
        );
        $this->assertTrue($this->_response->hasMessages(), 'New message is not added correctly.');

        /** Test message getting functionality. */
        $expectedMessage = array(
            \Magento\Webapi\Controller\Response::MESSAGE_TYPE_SUCCESS => array(
                array('key' => 'value', 'message' => 'Message text', 'code' => 200)
            )
        );
        $this->assertEquals($expectedMessage, $this->_response->getMessages(), 'Message is got incorrectly.');

        /** Test message clearing functionality. */
        $this->_response->clearMessages();
        $this->assertFalse($this->_response->hasMessages(), 'Message is not cleared.');
    }
}
