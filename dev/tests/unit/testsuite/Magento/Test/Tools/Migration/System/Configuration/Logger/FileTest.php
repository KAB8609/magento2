<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration//Acl/Db/LoggerAbstract.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration//Acl/Db/Logger/File.php';


require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration//System/Configuration/LoggerAbstract.php';

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration//System/Configuration/Logger/File.php';

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration//System/FileManager.php';

class Magento_Test_Tools_Migration_System_Configuration_Logger_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileManagerMock;

    public function setUp()
    {
        $this->_fileManagerMock = $this->getMock(
            'Magento\Tools\Migration\System\FileManager', array(), array(), '', false);
    }

    public function tearDown()
    {
        unset($this->_fileManagerMock);
    }

    public function testConstructWithValidFile()
    {
        new \Magento\Tools\Migration\System\Configuration\Logger\File('report.log', $this->_fileManagerMock);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithInValidFile()
    {
        new \Magento\Tools\Migration\System\Configuration\Logger\File(null, $this->_fileManagerMock);
    }

    public function testReport()
    {
        $model = new \Magento\Tools\Migration\System\Configuration\Logger\File('report.log', $this->_fileManagerMock);
        $this->_fileManagerMock->expects($this->once())->method('write')->with($this->stringEndsWith('report.log'));
        $model->report();
    }
}
