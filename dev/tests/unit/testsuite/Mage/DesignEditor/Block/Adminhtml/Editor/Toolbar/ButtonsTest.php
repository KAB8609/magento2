<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_ButtonsTest extends PHPUnit_Framework_TestCase
{
    /**
     * VDE toolbar buttons block
     *
     * @var Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons
     */
    protected $_block;

    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_urlBuilder;

    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_urlBuilder = $this->getMock('Mage_Backend_Model_Url', array('getUrl'), array(), '', false);

        $arguments = array(
            'urlBuilder' => $this->_urlBuilder
        );

        $this->_block = $helper->getBlock('Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons', $arguments);
    }

    public function testGetThemeId()
    {
        $this->_block->setThemeId(1);
        $this->assertEquals(1, $this->_block->getThemeId());
    }

    public function testSetThemeId()
    {
        $this->_block->setThemeId(2);
        $this->assertAttributeEquals(2, '_themeId', $this->_block);
    }

    public function testGetViewLayoutUrl()
    {
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->will($this->returnArgument(0));
        $this->assertEquals('*/*/getLayoutUpdate', $this->_block->getViewLayoutUrl());
    }

    public function testGetQuitUrl()
    {
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->will($this->returnArgument(0));
        $this->assertEquals('*/*/quit', $this->_block->getQuitUrl());
    }

    public function testGetNavigationModeUrl()
    {
        $this->_block->setThemeId(2);
        $mode = Mage_DesignEditor_Model_State::MODE_NAVIGATION;
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/*/launch', array('mode' => $mode, 'theme_id' => 2))
            ->will($this->returnValue("*/*/launch/mode/{$mode}/theme_id/2/"));

        $this->assertEquals("*/*/launch/mode/{$mode}/theme_id/2/",
            $this->_block->getNavigationModeUrl()
        );
    }

    public function testGetDesignModeUrl()
    {
        $this->_block->setThemeId(3);
        $mode = Mage_DesignEditor_Model_State::MODE_DESIGN;
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/*/launch', array('mode' => $mode, 'theme_id' => 3))
            ->will($this->returnValue("*/*/launch/mode/{$mode}/theme_id/3/"));

        $this->assertEquals("*/*/launch/mode/{$mode}/theme_id/3/", $this->_block->getDesignModeUrl());
    }

    public function testGetSaveTemporaryLayoutUpdateUrl()
    {
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->will($this->returnArgument(0));
        $this->assertEquals('*/*/saveTemporaryLayoutUpdate', $this->_block->getSaveTemporaryLayoutUpdateUrl());
    }
}
