<?php
/**
 * Test class for Mage_Core_Model_DataService_Request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_RequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test data for params
     */
    const SOME_INTERESTING_PARAMS = 'Some interesting params.';

    /**
     * Test getting the child node
     */
    public function testGetChild()
    {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')->disableOriginalConstructor()
            ->getMock();
        $requestMock->expects($this->once())->method('getParams')->will(
            $this->returnValue(
                self::SOME_INTERESTING_PARAMS
            )
        );
        $requestVisitor = new Mage_Core_Model_DataService_Request($requestMock);
        $this->assertEquals(self::SOME_INTERESTING_PARAMS, $requestVisitor->getChildNode('params'));
    }

    /**
     * Test getting a child node that does not exist.
     */
    public function testNotFound()
    {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')->disableOriginalConstructor()
            ->getMock();

        $requestVisitor = new Mage_Core_Model_DataService_Request($requestMock);
        $this->assertEquals(null, $requestVisitor->getChildNode('foo'));
    }
}