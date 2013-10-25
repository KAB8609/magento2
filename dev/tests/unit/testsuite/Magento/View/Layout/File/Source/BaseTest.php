<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Layout\File\Source\Base
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
        $this->_dirs = $this->getMock('Magento\App\Dir', array(), array(), '', false);
        $this->_dirs->expects($this->any())->method('getDir')->will($this->returnArgument(0));
        $this->_fileFactory = $this->getMock('Magento\View\Layout\File\Factory', array(), array(), '', false);
        $this->_model = new \Magento\View\Layout\File\Source\Base(
            $this->_filesystem, $this->_dirs, $this->_fileFactory
        );
    }

    /**
     * @param array $files
     * @param string $filePath
     *
     * @dataProvider dataProvider
     */
    public function testGetFiles($files, $filePath)
    {
        $theme = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');
        $theme->expects($this->once())->method('getArea')->will($this->returnValue('area'));

        $handlePath = 'code/Module/%s/view/area/layout/%s.xml';
        $returnKeys = array();
        foreach ($files as $file) {
            $returnKeys[] = sprintf($handlePath, $file['module'], $file['handle']);
        }

        $this->_filesystem
            ->expects($this->once())
            ->method('searchKeys')
            ->with('code', "*/*/view/area/layout/{$filePath}.xml")
            ->will($this->returnValue($returnKeys))
        ;

        $checkResult = array();
        foreach ($files as $key => $file) {
            $moduleName = 'Module_' . $file['module'];
            $checkResult[$key] = new \Magento\View\Layout\File(
                $file['handle'] . '.xml',
                $moduleName,
                $theme
            );

            $this->_fileFactory
                ->expects($this->at($key))
                ->method('create')
                ->with(sprintf($handlePath, $file['module'], $file['handle']), $moduleName)
                ->will($this->returnValue($checkResult[$key]))
            ;
        }

        $this->assertSame($checkResult, $this->_model->getFiles($theme, $filePath));
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array(
                array(
                    array('handle' => '1', 'module' => 'One'),
                    array('handle' => '2', 'module' => 'One'),
                    array('handle' => '3', 'module' => 'Two'),
                ),
                '*',
            ),
            array(
                array(
                    array('handle' => 'preset/4', 'module' => 'Fourth'),
                ),
                'preset/4',
            ),
        );
    }
}
