<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Index
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Index_Model_EntryPoint_IndexerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Index_Model_EntryPoint_Indexer
     */
    protected $_entryPoint;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_primaryConfig;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var string
     */
    protected $_reportDir;

    protected function setUp()
    {
        $this->_reportDir = 'tmp' . DIRECTORY_SEPARATOR . 'reports';
        $this->_primaryConfig = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false);
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_entryPoint = $this->getMock(
            'Mage_Index_Model_EntryPoint_Indexer',
            array('_setGlobalObjectManager'),
            array($this->_reportDir, $this->_filesystem, $this->_primaryConfig, $this->_objectManager)
        );
    }

    public function testProcessRequest()
    {
        $process = $this->getMock('Mage_Index_Model_Process', array(), array(), '', false);
        $processIndexer = $this->getMockForAbstractClass(
            'Mage_Index_Model_Indexer_Abstract',
            array(),
            '',
            false
        );
        $processIndexer->expects($this->any())->method('isVisible')->will($this->returnValue(true));
        $process->expects($this->any())->method('getIndexer')->will($this->returnValue($processIndexer));
        $process->expects($this->once())->method('reindexEverything')->will($this->returnSelf());

        $indexer = $this->getMock('Mage_Index_Model_Indexer', array(), array(), '', false);
        $indexer->expects($this->once())
            ->method('getProcessesCollection')
            ->will($this->returnValue(array($process)));

        $this->_objectManager->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap(
                array(
                    array('Mage_Index_Model_Indexer', array(), $indexer),
                )
            ));
        // check that report directory is cleaned
        $this->_filesystem->expects($this->once())
            ->method('delete')
            ->with($this->_reportDir, dirname($this->_reportDir));

        $this->_entryPoint->processRequest();
    }
}
