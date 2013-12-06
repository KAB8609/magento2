<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Element\Template
     */
    protected $_block;

    /**
     * @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var \Magento\View\TemplateEngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_templateEngine;

    /**
     * @var \Magento\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewFileSystem;

    protected function setUp()
    {
        $dirMap = array(
            array(\Magento\App\Dir::APP, __DIR__),
            array(\Magento\App\Dir::THEMES, __DIR__ . '/design'),
        );
        $dirs = $this->getMock('Magento\App\Dir', array(), array(), '', false, false);
        $dirs->expects($this->any())->method('getDir')->will($this->returnValueMap($dirMap));

        $this->_viewFileSystem = $this->getMock('\Magento\View\FileSystem', array(), array(), '', false);

        $this->_filesystem = $this->getMock('\Magento\Filesystem', array(), array(), '', false);

        $this->_templateEngine =
            $this->getMock('Magento\View\TemplateEnginePool', array('render', 'get'), array(), '', false);

        $this->_templateEngine->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->_templateEngine));

        $appState = $this->getMock('Magento\App\State', array('getAreaCode'), array(), '', false);
        $appState->expects($this->any())->method('getAreaCode')->will($this->returnValue('frontend'));
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $helper->getObject('Magento\View\Element\Template', array(
            'filesystem' => $this->_filesystem,
            'enginePool' => $this->_templateEngine,
            'viewFileSystem' => $this->_viewFileSystem,
            'appState' => $appState,
            'dirs' => $dirs,
            'data' => array('template' => 'template.phtml', 'module_name' => 'Fixture_Module')
            )
        );
    }

    public function testGetTemplateFile()
    {
        $params = array('module' => 'Fixture_Module', 'area' => 'frontend');
        $this->_viewFileSystem->expects($this->once())->method('getFilename')->with('template.phtml', $params);
        $this->_block->getTemplateFile();
    }

    public function testFetchView()
    {
        $this->expectOutputString('');

        $this->_filesystem
            ->expects($this->once())
            ->method('isPathInDirectory')
            ->with('template.phtml', __DIR__)
            ->will($this->returnValue(true))
        ;
        $this->_filesystem
            ->expects($this->once())->method('isFile')->with('template.phtml')->will($this->returnValue(true));

        $output = '<h1>Template Contents</h1>';
        $vars = array('var1' => 'value1', 'var2' => 'value2');
        $this->_templateEngine
            ->expects($this->once())
            ->method('render')
            ->with($this->identicalTo($this->_block), 'template.phtml', $vars)
            ->will($this->returnValue($output))
        ;
        $this->_block->assign($vars);
        $this->assertEquals($output, $this->_block->fetchView('template.phtml'));
    }
}
