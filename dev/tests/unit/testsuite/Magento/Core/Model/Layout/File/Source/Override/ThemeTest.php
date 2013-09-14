<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_File_Source_Override_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\File\Source\Override\Theme
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_filesystem;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_dirs;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_fileFactory;

    protected function setUp()
    {
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_dirs = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false);
        $this->_dirs->expects($this->any())->method('getDir')->will($this->returnArgument(0));
        $this->_fileFactory = $this->getMock('Magento\Core\Model\Layout\File\Factory', array(), array(), '', false);
        $this->_model = new \Magento\Core\Model\Layout\File\Source\Override\Theme(
            $this->_filesystem, $this->_dirs, $this->_fileFactory
        );
    }

    public function testGetFiles()
    {
        $grandparentTheme = $this->getMockForAbstractClass('Magento\Core\Model\ThemeInterface');
        $grandparentTheme->expects($this->once())->method('getCode')->will($this->returnValue('grand_parent_theme'));

        $parentTheme = $this->getMockForAbstractClass('Magento\Core\Model\ThemeInterface');
        $parentTheme->expects($this->once())->method('getCode')->will($this->returnValue('parent_theme'));
        $parentTheme->expects($this->once())->method('getParentTheme')->will($this->returnValue($grandparentTheme));

        $theme = $this->getMockForAbstractClass('Magento\Core\Model\ThemeInterface');
        $theme->expects($this->once())->method('getFullPath')->will($this->returnValue('area/theme_path'));
        $theme->expects($this->once())->method('getParentTheme')->will($this->returnValue($parentTheme));

        $filePathOne = 'design/area/theme_path/Module_One/layout/override/parent_theme/1.xml';
        $filePathTwo = 'design/area/theme_path/Module_Two/layout/override/grand_parent_theme/2.xml';
        $this->_filesystem
            ->expects($this->once())
            ->method('searchKeys')
            ->with('design', 'area/theme_path/*_*/layout/override/*/*.xml')
            ->will($this->returnValue(array($filePathOne, $filePathTwo)))
        ;

        $fileOne = new \Magento\Core\Model\Layout\File('1.xml', 'Module_One', $parentTheme);
        $fileTwo = new \Magento\Core\Model\Layout\File('2.xml', 'Module_Two', $grandparentTheme);
        $this->_fileFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValueMap(array(
                array($filePathOne, 'Module_One', $parentTheme, $fileOne),
                array($filePathTwo, 'Module_Two', $grandparentTheme, $fileTwo),
            )))
        ;

        $this->assertSame(array($fileOne, $fileTwo), $this->_model->getFiles($theme));
    }

    public function testGetFilesWrongAncestor()
    {
        $filePath = 'design/area/theme_path/Module_One/layout/override/parent_theme/1.xml';
        $this->setExpectedException(
            'Magento\Core\Exception',
            "Trying to override layout file '$filePath' for theme 'parent_theme'"
                . ", which is not ancestor of theme 'theme_path'"
        );

        $theme = $this->getMockForAbstractClass('Magento\Core\Model\ThemeInterface');
        $theme->expects($this->once())->method('getFullPath')->will($this->returnValue('area/theme_path'));
        $theme->expects($this->once())->method('getParentTheme')->will($this->returnValue(null));
        $theme->expects($this->once())->method('getCode')->will($this->returnValue('theme_path'));

        $this->_filesystem
            ->expects($this->once())
            ->method('searchKeys')
            ->with('design', 'area/theme_path/*_*/layout/override/*/*.xml')
            ->will($this->returnValue(array($filePath)))
        ;
        $this->_model->getFiles($theme);
    }
}
