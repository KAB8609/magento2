<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Layout\File\Source\Override;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\File\Source\Override\Base
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_filesystem;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_dirs;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_fileFactory;

    protected function setUp()
    {
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_dirs = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false);
        $this->_dirs->expects($this->any())->method('getDir')->will($this->returnArgument(0));
        $this->_fileFactory = $this->getMock('Magento\Core\Model\Layout\File\Factory', array(), array(), '', false);
        $this->_model = new \Magento\Core\Model\Layout\File\Source\Override\Base(
            $this->_filesystem, $this->_dirs, $this->_fileFactory
        );
    }

    public function testGetFiles()
    {
        $theme = $this->getMockForAbstractClass('Magento\View\Design\Theme');
        $theme->expects($this->once())->method('getFullPath')->will($this->returnValue('area/theme/path'));

        $this->_filesystem
            ->expects($this->once())
            ->method('searchKeys')
            ->with('design', 'area/theme/path/*_*/layout/override/*.xml')
            ->will($this->returnValue(array(
                'design/area/theme/path/Module_One/layout/override/1.xml',
                'design/area/theme/path/Module_One/layout/override/2.xml',
                'design/area/theme/path/Module_Two/layout/override/3.xml',
            )))
        ;

        $fileOne = new \Magento\Core\Model\Layout\File('1.xml', 'Module_One');
        $fileTwo = new \Magento\Core\Model\Layout\File('2.xml', 'Module_One');
        $fileThree = new \Magento\Core\Model\Layout\File('3.xml', 'Module_Two');
        $this->_fileFactory
            ->expects($this->exactly(3))
            ->method('create')
            ->will($this->returnValueMap(array(
                array('design/area/theme/path/Module_One/layout/override/1.xml', 'Module_One', null, $fileOne),
                array('design/area/theme/path/Module_One/layout/override/2.xml', 'Module_One', null, $fileTwo),
                array('design/area/theme/path/Module_Two/layout/override/3.xml', 'Module_Two', null, $fileThree),
            )))
        ;

        $this->assertSame(array($fileOne, $fileTwo, $fileThree), $this->_model->getFiles($theme));
    }
}
