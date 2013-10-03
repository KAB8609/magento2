<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Layout\File\Source;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\File\Source\Base
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
        $this->_model = new \Magento\Core\Model\Layout\File\Source\Base(
            $this->_filesystem, $this->_dirs, $this->_fileFactory
        );
    }

    public function testGetFiles()
    {
        $theme = $this->getMockForAbstractClass('Magento\Core\Model\ThemeInterface');
        $theme->expects($this->once())->method('getArea')->will($this->returnValue('area'));

        $this->_filesystem
            ->expects($this->once())
            ->method('searchKeys')
            ->with('code', '*/*/view/area/layout/*.xml')
            ->will($this->returnValue(array(
                'code/Module/One/view/area/layout/1.xml',
                'code/Module/One/view/area/layout/2.xml',
                'code/Module/Two/view/area/layout/3.xml',
            )))
        ;

        $fileOne = new \Magento\Core\Model\Layout\File('1.xml', 'Module_One');
        $fileTwo = new \Magento\Core\Model\Layout\File('2.xml', 'Module_One');
        $fileThree = new \Magento\Core\Model\Layout\File('3.xml', 'Module_Two');
        $this->_fileFactory
            ->expects($this->exactly(3))
            ->method('create')
            ->will($this->returnValueMap(array(
                array('code/Module/One/view/area/layout/1.xml', 'Module_One', null, $fileOne),
                array('code/Module/One/view/area/layout/2.xml', 'Module_One', null, $fileTwo),
                array('code/Module/Two/view/area/layout/3.xml', 'Module_Two', null, $fileThree),
            )))
        ;

        $this->assertSame(array($fileOne, $fileTwo, $fileThree), $this->_model->getFiles($theme));
    }
}
