<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Index_Model_EventRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Index_Model_EventRepository
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventCollection;

    protected function setUp()
    {
        $this->_eventCollection = $this->getMock(
            'Mage_Index_Model_Resource_Event_Collection', array(), array(), '', false
        );
        $collectionFactory = $this->getMock(
            'Mage_Index_Model_Resource_Event_CollectionFactory', array('create'), array(), '', false
        );
        $collectionFactory->expects($this->any())->method('create')->will($this->returnValue($this->_eventCollection));
        $this->_model = new Mage_Index_Model_EventRepository($collectionFactory);
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testHasUnprocessedReturnsTrueIfUnprocessedEventsExist()
    {
        $process = 'proc1';
        $this->_eventCollection->expects($this->once())->method('getSize')->will($this->returnValue(1));
        $this->_eventCollection->expects($this->once())->method('addProcessFilter')->with($process);
        $this->assertTrue($this->_model->hasUnprocessed($process));
    }

    public function testHasUnprocessedReturnsFalseIfUnprocessedEventsExist()
    {
        $process = 'proc2';
        $this->_eventCollection->expects($this->once())->method('getSize')->will($this->returnValue(0));
        $this->_eventCollection->expects($this->once())->method('addProcessFilter')->with($process);
        $this->assertFalse($this->_model->hasUnprocessed($process));
    }

    public function testGetUnprocessedReturnsConfiguredCollectionOfEvents()
    {
        $process = $this->getMock('Mage_Index_Model_Process', array(), array(), '', false);
        $this->_eventCollection->expects($this->once())->method('addProcessFilter')
            ->with($process, Mage_Index_Model_Process::EVENT_STATUS_NEW);
        $this->assertEquals($this->_eventCollection, $this->_model->getUnprocessed($process));
    }
}