<?php
/**
 * Mage_Webhook_Model_Resource_Job_Collection
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Job_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $mockDBAdapter = $this->getMockBuilder('Magento_DB_Adapter_Pdo_Mysql')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mockResourceEvent = $this->getMockBuilder('Mage_Webhook_Model_Resource_Job')
            ->disableOriginalConstructor()
            ->getMock();
        $mockResourceEvent->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue($mockDBAdapter));

        $mockObjectManager = $this->_setMageObjectManager();
        $mockObjectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Mage_Webhook_Model_Resource_Job'))
            ->will($this->returnValue($mockResourceEvent));
    }

    public function tearDown()
    {
        // Unsets object manager
        Mage::reset();
    }

    public function testConstructor()
    {
        $mockFetchStrategy = $this->getMockBuilder('Magento_Data_Collection_Db_FetchStrategyInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $collection = new Mage_Webhook_Model_Resource_Job_Collection($mockFetchStrategy);
        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Job_Collection', $collection);
        $this->assertEquals('Mage_Webhook_Model_Resource_Job', $collection->getResourceModelName());
    }

    public function testSetPageLimit()
    {
        $mockFetchStrategy = $this->getMockBuilder('Magento_Data_Collection_Db_FetchStrategyInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $selectMock = $this->getMockBuilder('Zend_Db_Select')
            ->disableOriginalConstructor()
            ->getMock();
        $selectMock->expects($this->once())
            ->method('limitPage');

        $connMock = $this->getMockBuilder('Magento_DB_Adapter_Pdo_Mysql')
            ->disableOriginalConstructor()
            ->getMock();
        // this method is simply used to set a value, it is not being tested
        $connMock->expects($this->any())
            ->method('select')
            ->withAnyParameters()
            ->will($this->returnValue($selectMock));

        $collection = new Mage_Webhook_Model_Resource_Job_Collection($mockFetchStrategy);
        $collection->setConnection($connMock);
        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Job_Collection', $collection->setPageLimit());
    }

    /**
     * Makes sure that Mage has a mock object manager set, and returns that instance.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _setMageObjectManager()
    {
        Mage::reset();
        $mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        Mage::setObjectManager($mockObjectManager);

        return $mockObjectManager;
    }
}