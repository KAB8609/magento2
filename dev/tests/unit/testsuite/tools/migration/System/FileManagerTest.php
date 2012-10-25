<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../') . '/tools/migration/System/FileManager.php';

class Tools_Migration_System_FileManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tools_Migration_System_FileManager
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_writerMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Tools_Migration_System_FileReader', array(), array(), '', false);
        $this->_writerMock = $this->getMock('Tools_Migration_System_Writer_MemoryMock', array(), array(), '', false);
        $this->_model = new Tools_Migration_System_FileManager($this->_readerMock, $this->_writerMock);
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_readerMock = null;
        $this->_writerMock = null;
    }

    public function testWrite()
    {
        $this->_writerMock->expects($this->once())->method('write')->with('someFile', 'someContent');
        $this->_model->write('someFile', 'someContent');
    }

    public function testRemove()
    {
        $this->_writerMock->expects($this->once())->method('remove')->with('someFile');
        $this->_model->remove('someFile');
    }

    public function testGetContents()
    {
        $this->_readerMock->expects($this->once())->method('remove')->with('someFile')->will($this->returnValue('123'));
        $this->assertEquals('123', $this->_model->getContents('someFile'));
    }

    public function testGetFileList()
    {
        $expected = array('file1', 'file2');
        $this->_readerMock->expects($this->once())->method('remove')->with('pattern')
            ->will($this->returnValue($expected));

        $this->assertEquals($expected, $this->_model->getFileList('pattern'));
    }
}
