<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerSegment_Model_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_CustomerSegment_Model_Customer
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_registry;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_customerSession;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_resource;

    /**
     * @var array
     */
    private $_fixtureSegmentIds = array(123, 456);

    protected function setUp()
    {
        $this->_registry = $this->getMock('Magento_Core_Model_Registry', array('registry'), array(), '', false);

        $website = new Magento_Object(array('id' => 5));
        $storeManager = $this->getMockForAbstractClass(
            'Magento_Core_Model_StoreManagerInterface', array('getWebsite'), '', false
        );
        $storeManager->expects($this->once())->method('getWebsite')->will($this->returnValue($website));

        $this->_customerSession = $this->getMock(
            'Magento_Customer_Model_Session', array('getCustomer'), array(), '', false
        );

        $this->_resource = $this->getMock(
            'Magento_CustomerSegment_Model_Resource_Customer',
            array('getCustomerWebsiteSegments', 'getIdFieldName'),
            array($this->getMock('Magento_Core_Model_Resource', array(), array(), '', false))
        );

        $this->_model = new Magento_CustomerSegment_Model_Customer(
            $this->getMock('Magento_Core_Model_Context', array(), array(), '', false),
            $this->_registry,
            $storeManager,
            $this->_customerSession,
            $this->_resource
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_registry = null;
        $this->_customerSession = null;
        $this->_resource = null;
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInRegistry()
    {
        $customer = new Magento_Object(array('id' => 100500));
        $this->_registry
            ->expects($this->once())->method('registry')->with('segment_customer')->will($this->returnValue($customer));
        $this->_resource
            ->expects($this->once())
            ->method('getCustomerWebsiteSegments')
            ->with(100500, 5)
            ->will($this->returnValue($this->_fixtureSegmentIds))
        ;
        $this->assertEquals($this->_fixtureSegmentIds, $this->_model->getCurrentCustomerSegmentIds());
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInRegistryNoId()
    {
        $customer = new Magento_Object();
        $this->_registry
            ->expects($this->once())->method('registry')->with('segment_customer')->will($this->returnValue($customer));
        $this->_customerSession->setData('customer_segment_ids', array(5 => $this->_fixtureSegmentIds));
        $this->assertEquals($this->_fixtureSegmentIds, $this->_model->getCurrentCustomerSegmentIds());
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInSession()
    {
        $customer = new Magento_Object(array('id' => 100500));
        $this->_customerSession->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));
        $this->_resource
            ->expects($this->once())
            ->method('getCustomerWebsiteSegments')
            ->with(100500, 5)
            ->will($this->returnValue($this->_fixtureSegmentIds))
        ;
        $this->assertEquals($this->_fixtureSegmentIds, $this->_model->getCurrentCustomerSegmentIds());
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInSessionNoId()
    {
        $customer = new Magento_Object();
        $this->_customerSession->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));
        $this->_customerSession->setData('customer_segment_ids', array(5 => $this->_fixtureSegmentIds));
        $this->assertEquals($this->_fixtureSegmentIds, $this->_model->getCurrentCustomerSegmentIds());
    }
}