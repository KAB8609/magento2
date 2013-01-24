<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Block_TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testGetTemplateFile()
    {
        $design = $this->getMock('Mage_Core_Model_Design_Package', array('getFilename'), array(), '', false);
        $template = 'fixture';
        $area = 'areaFixture';
        $block = new Mage_Core_Block_Template(
            $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Model_Url', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Model_Translate', array(), array($design), '', false, false),
            $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false),
            $design,
            $this->getMock('Mage_Core_Model_Session', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Model_Store_Config', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Logger', array(), array(), '', false),
            $this->getMock('Magento_Filesystem', array(), array(), '', false),
            array('template' => $template, 'area' => $area)
        );

        $params = array('module' => 'Mage_Core', 'area' => $area);
        $design->expects($this->once())->method('getFilename')->with($template, $params);
        $block->getTemplateFile();
    }

    /**
     * @param string $filename
     * @param string $expectedOutput
     * @dataProvider fetchViewDataProvider
     */
    public function testFetchView($filename, $expectedOutput)
    {
        $map = array(
            array(Mage_Core_Model_Dir::APP, __DIR__),
            array(Mage_Core_Model_Dir::THEMES, __DIR__ . 'design'),
        );
        $dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false, false);
        $dirMock->expects($this->any())->method('getDir')->will($this->returnValueMap($map));
        $layout = $this->getMock('Mage_Core_Model_Layout', array('isDirectOutput'), array(), '', false);
        $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local);
        $design = $this->getMock('Mage_Core_Model_Design_Package', array(), array($filesystem));
        $block = $this->getMock('Mage_Core_Block_Template', array('getShowTemplateHints'), array(
            $this->getMock('Mage_Core_Controller_Request_Http'),
            $layout,
            $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Model_Url', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Model_Translate', array(),
                array(
                    $design,
                    $this->getMock('Mage_Core_Model_Locale_Hierarchy_Loader', array(), array(), '', false, false)
                )
            ),
            $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Session', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Model_Store_Config', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false, false),
            $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false, false),
            $dirMock,
            $this->getMock('Mage_Core_Model_Logger', array('log'), array(), '', false),
            $filesystem
        ));
        $layout->expects($this->once())->method('isDirectOutput')->will($this->returnValue(false));

        $this->assertSame($block, $block->assign(array('varOne' => 'value1', 'varTwo' => 'value2')));
        $this->assertEquals($expectedOutput, $block->fetchView(__DIR__ . "/_files/{$filename}"));
    }

    /**
     * @return array
     */
    public function fetchViewDataProvider()
    {
        return array(
            array('template_test_assign.phtml', 'value1, value2'),
            array('invalid_file', ''),
        );
    }
}
