<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Module;

class DirTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Module\Dir
     */
    protected $_model;

    /**
     * @var \Magento\App\Dir|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationDirs;

    protected function setUp()
    {
        $this->_applicationDirs = $this->getMock('Magento\App\Dir', array(), array(), '', false, false);
        $this->_applicationDirs
            ->expects($this->once())
            ->method('getDir')
            ->with(\Magento\App\Dir::MODULES)
            ->will($this->returnValue('app' . DIRECTORY_SEPARATOR . 'code'))
        ;
        $this->_model = new \Magento\Core\Model\Module\Dir($this->_applicationDirs);
    }

    public function testGetDirModuleRoot()
    {
        $this->assertEquals(
            str_replace('/', DIRECTORY_SEPARATOR, 'app/code/Test/Module'),
            $this->_model->getDir('Test_Module')
        );
    }

    public function testGetDirModuleSubDir()
    {
        $this->assertEquals(
            str_replace('/', DIRECTORY_SEPARATOR, 'app/code/Test/Module/etc'),
            $this->_model->getDir('Test_Module', 'etc')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Directory type 'unknown' is not recognized
     */
    public function testGetDirModuleSubDirUnknown()
    {
        $this->_model->getDir('Test_Module', 'unknown');
    }
}
