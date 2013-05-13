<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Index
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Model_ProcessTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test exception message
     */
    const EXCEPTION_MESSAGE = 'Test exception message';

    /**
     * Indexer used for test
     */
    const INDEXER_CODE = 'catalog_url';

    /**
     * @var array
     */
    protected $_indexerMatchData = array(
        'new_data' => array(Mage_Catalog_Model_Indexer_Url::EVENT_MATCH_RESULT_KEY => true)
    );

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Index_Model_Process
     */
    protected $_model;

    /**
     * @var Mage_Index_Model_Process_File
     */
    protected $_processFile;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventRepositoryMock;

    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();

        $this->_eventRepositoryMock = $this->getMock(
            'Mage_Index_Model_EventRepository', array(), array(), '', false
        );

        // get existing indexer process
        $this->_model = $this->_objectManager->create(
            'Mage_Index_Model_Process', array('eventRepository' => $this->_eventRepositoryMock)
        );
        $this->_model->load(self::INDEXER_CODE, 'indexer_code');
        if ($this->_model->isObjectNew()) {
            $this->markTestIncomplete('Can\'t run test without ' . self::INDEXER_CODE . ' indexer.');
        }

        // get new process file instance for current indexer
        /** @var $lockStorage Mage_Index_Model_Lock_Storage */
        $lockStorage = $this->_objectManager->create('Mage_Index_Model_Lock_Storage');
        $this->_processFile = $lockStorage->getFile($this->_model->getId());
    }

    /**
     * @return array
     */
    public function safeProcessEventDataProvider()
    {
        return array(
            'not_matched' => array(
                '$eventData' => array(),
            ),
            'locked' => array(
                '$eventData' => $this->_indexerMatchData,
                '$needLock'  => true,
            ),
            'matched' => array(
                '$eventData' => $this->_indexerMatchData,
            ),
        );
    }

    /**
     * @param array $eventData
     * @param bool $needLock
     *
     * @dataProvider safeProcessEventDataProvider
     */
    public function testSafeProcessEvent(array $eventData, $needLock = false)
    {
        if ($needLock) {
            $this->_processFile->processLock();
        }

        $event = $this->_objectManager->create('Mage_Index_Model_Event', array('data' => $eventData));
        $this->assertEquals($this->_model, $this->_model->safeProcessEvent($event));

        if ($needLock) {
            $this->_processFile->processUnlock();
        }

        $this->assertFalse($this->_processFile->isProcessLocked(true));
    }

    public function testSafeProcessEventException()
    {
        // prepare mock that throws exception
        /** @var $eventMock Mage_Index_Model_Event */
        $eventMock = $this->getMock('Mage_Index_Model_Event', array('setProcess'), array(), '', false);
        $eventMock->setData($this->_indexerMatchData);
        $exceptionMessage = self::EXCEPTION_MESSAGE;
        $eventMock->expects($this->any())
            ->method('setProcess')
            ->will($this->returnCallback(
                function () use ($exceptionMessage) {
                    throw new Exception($exceptionMessage);
                }
            ));

        // can't use @expectedException because we need to assert indexer lock status
        try {
            $this->_model->safeProcessEvent($eventMock);
        } catch (Exception $e) {
            $this->assertEquals(self::EXCEPTION_MESSAGE, $e->getMessage());
        }

        $this->assertFalse($this->_processFile->isProcessLocked(true));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testReindexAllDoesntTriggerUnprocessedEventFetchingInManualMode()
    {
        $collection = $this->_objectManager->create('Mage_Index_Model_Resource_Event_Collection');
        $this->_model->setMode(Mage_Index_Model_Process::MODE_REAL_TIME);
        $this->_model->setStatus(Mage_Index_Model_Process::STATUS_PENDING);
        $this->_eventRepositoryMock->expects($this->once())->method('getUnprocessed')
            ->will($this->returnValue($collection));
        $this->_eventRepositoryMock->expects($this->never())->method('hasUnprocessed');
        $this->_model->reindexAll();
    }
}
