<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\EntryPoint;

class IndexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Index\Model\EntryPoint\Indexer
     */
    protected $_entryPoint;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_primaryConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var string
     */
    protected $_reportDir;

    protected function setUp()
    {
        $this->_reportDir = 'tmp' . DIRECTORY_SEPARATOR . 'reports';
        $this->_primaryConfig = $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false);
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_entryPoint = $this->getMock('\Magento\Index\Model\EntryPoint\Indexer', array('_initErrorHandler'),
            array($this->_reportDir, $this->_filesystem, $this->_primaryConfig, $this->_objectManager));
    }

    public function testProcessRequest()
    {
        $process = $this->getMock('Magento\Index\Model\Process', array(), array(), '', false);
        $processIndexer = $this->getMockForAbstractClass(
            'Magento\Index\Model\Indexer\AbstractIndexer',
            array(),
            '',
            false
        );
        $processIndexer->expects($this->any())->method('isVisible')->will($this->returnValue(true));
        $process->expects($this->any())->method('getIndexer')->will($this->returnValue($processIndexer));
        $process->expects($this->once())->method('reindexEverything')->will($this->returnSelf());

        $indexer = $this->getMock('Magento\Index\Model\Indexer', array(), array(), '', false);
        $indexer->expects($this->once())
            ->method('getProcessesCollection')
            ->will($this->returnValue(array($process)));

        $this->_objectManager->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap(
                array(
                    array('Magento\Index\Model\Indexer', array(), $indexer),
                )
            ));
        // check that report directory is cleaned
        $this->_filesystem->expects($this->once())
            ->method('delete')
            ->with($this->_reportDir, dirname($this->_reportDir));

        $this->_entryPoint->processRequest();
    }
}
