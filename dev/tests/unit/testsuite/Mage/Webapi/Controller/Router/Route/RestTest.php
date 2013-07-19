<?php
/**
 * Test Rest router route.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Router_Route_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Router_Route_Rest */
    protected $_restRoute;

    protected function setUp()
    {
        /** Init SUT. */
        $this->_restRoute = new Mage_Webapi_Controller_Router_Route_Rest('route');
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restRoute);
        parent::tearDown();
    }

    /**
     * Test setServiceName and getServiceName methods.
     */
    public function testResourceName()
    {
        /** Assert that new object has no Resource name set. */
        $this->assertNull($this->_restRoute->getServiceId(), 'New object has a set Resource name.');
        /** Set Resource name. */
        $resourceName = 'Resource name';
        $this->_restRoute->setServiceId($resourceName);
        /** Assert that Resource name was set. */
        $this->assertEquals($resourceName, $this->_restRoute->getServiceId(), 'Resource name is wrong.');
    }

    /**
     * Test setServiceType and getServiceType methods.
     */
    public function testResourceType()
    {
        /** Assert that new object has no Resource type set. */
        $this->assertNull($this->_restRoute->getHttpMethod(), 'New object has a set Resource type.');
        /** Set Resource type. */
        $resourceType = 'Resource type';
        $this->_restRoute->setHttpMethod($resourceType);
        /** Assert that Resource type was set. */
        $this->assertEquals($resourceType, $this->_restRoute->getHttpMethod(), 'Resource type is wrong.');
    }
}
