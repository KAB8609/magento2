<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_File_Source_AggregateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\File\Source\Aggregated
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_fileList;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_baseFiles;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_themeFiles;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_overridingBaseFiles;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_overridingThemeFiles;

    protected function setUp()
    {
        $this->_fileList = $this->getMock('Magento\Core\Model\Layout\File\ListFile', array(), array(), '', false);
        $this->_baseFiles = $this->getMockForAbstractClass('\Magento\Core\Model\Layout\File\SourceInterface');
        $this->_themeFiles = $this->getMockForAbstractClass('\Magento\Core\Model\Layout\File\SourceInterface');
        $this->_overridingBaseFiles = $this->getMockForAbstractClass('\Magento\Core\Model\Layout\File\SourceInterface');
        $this->_overridingThemeFiles = $this->getMockForAbstractClass('\Magento\Core\Model\Layout\File\SourceInterface');
        $fileListFactory =
            $this->getMock('Magento\Core\Model\Layout\File\FileList\Factory', array(), array(), '', false);
        $fileListFactory->expects($this->once())->method('create')->will($this->returnValue($this->_fileList));
        $this->_model = new \Magento\Core\Model\Layout\File\Source\Aggregated(
            $fileListFactory, $this->_baseFiles, $this->_themeFiles,
            $this->_overridingBaseFiles, $this->_overridingThemeFiles
        );
    }

    public function testGetFiles()
    {
        $parentTheme = $this->getMockForAbstractClass('\Magento\Core\Model\ThemeInterface');
        $theme = $this->getMockForAbstractClass('\Magento\Core\Model\ThemeInterface');
        $theme->expects($this->once())->method('getParentTheme')->will($this->returnValue($parentTheme));

        $files = array(
            new \Magento\Core\Model\Layout\File('0.xml', 'Module_One'),
            new \Magento\Core\Model\Layout\File('1.xml', 'Module_One', $parentTheme),
            new \Magento\Core\Model\Layout\File('2.xml', 'Module_One', $parentTheme),
            new \Magento\Core\Model\Layout\File('3.xml', 'Module_One', $parentTheme),
            new \Magento\Core\Model\Layout\File('4.xml', 'Module_One', $theme),
            new \Magento\Core\Model\Layout\File('5.xml', 'Module_One', $theme),
            new \Magento\Core\Model\Layout\File('6.xml', 'Module_One', $theme),
        );

        $this->_baseFiles
            ->expects($this->once())->method('getFiles')->with($theme)->will($this->returnValue(array($files[0])));

        $this->_themeFiles
            ->expects($this->at(0))->method('getFiles')->with($parentTheme)->will($this->returnValue(array($files[1])));
        $this->_overridingBaseFiles
            ->expects($this->at(0))->method('getFiles')->with($parentTheme)->will($this->returnValue(array($files[2])));
        $this->_overridingThemeFiles
            ->expects($this->at(0))->method('getFiles')->with($parentTheme)->will($this->returnValue(array($files[3])));

        $this->_themeFiles
            ->expects($this->at(1))->method('getFiles')->with($theme)->will($this->returnValue(array($files[4])));
        $this->_overridingBaseFiles
            ->expects($this->at(1))->method('getFiles')->with($theme)->will($this->returnValue(array($files[5])));
        $this->_overridingThemeFiles
            ->expects($this->at(1))->method('getFiles')->with($theme)->will($this->returnValue(array($files[6])));

        $this->_fileList->expects($this->at(0))->method('add')->with(array($files[0]));
        $this->_fileList->expects($this->at(1))->method('add')->with(array($files[1]));
        $this->_fileList->expects($this->at(2))->method('replace')->with(array($files[2]));
        $this->_fileList->expects($this->at(3))->method('replace')->with(array($files[3]));
        $this->_fileList->expects($this->at(4))->method('add')->with(array($files[4]));
        $this->_fileList->expects($this->at(5))->method('replace')->with(array($files[5]));
        $this->_fileList->expects($this->at(6))->method('replace')->with(array($files[6]));

        $this->_fileList->expects($this->atLeastOnce())->method('getAll')->will($this->returnValue($files));

        $this->assertSame($files, $this->_model->getFiles($theme));
    }
}
