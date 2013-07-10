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

class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_CustomTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        $this->_urlBuilder = $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            'Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom',
            array(
                'config' => $this->getMock('Mage_Core_Model_Config', array(), array(), '', false),
                'urlBuilder' => $this->_urlBuilder
        ));
    }

    public function tearDown()
    {
        $this->_model = null;
        $this->_urlBuilder = null;
    }

    /**
     * @dataProvider prepareTheme
     * @covers Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom::getDownloadCustomCssUrl
     */
    public function testGetDownloadCustomCssUrl($theme)
    {
        $expectedUrl = 'some_url';

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/system_design_theme/downloadCustomCss', array('theme_id' => $theme->getThemeId()))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->getDownloadCustomCssUrl($theme));
    }

    /**
     * @dataProvider prepareTheme
     */
    public function testGetSaveCustomCssUrl($theme)
    {
        $expectedUrl = 'some_url';

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/system_design_editor_tools/saveCssContent', array('theme_id' => $theme->getThemeId()))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->getSaveCustomCssUrl($theme));
    }

    public function testGetCustomCssContent()
    {
        $expectedContent = 'New file content';

        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->getMock(
            'Mage_Core_Model_Theme', array('getCustomizationData', 'getFirstItem'), array(), '', false
        );

        /** @var $cssFile Mage_Core_Model_Theme_Customization_File_Css */
        $cssFile = $this->getMock(
            'Mage_Core_Model_Theme_Customization_File_Css', array('getContent'), array(), '', false
        );

        $theme->expects($this->once())
            ->method('getCustomizationData')
            ->with(Mage_Core_Model_Theme_Customization_File_Css::TYPE)
            ->will($this->returnValue($theme));

        $theme->expects($this->once())
            ->method('getFirstItem')
            ->will($this->returnValue($cssFile));

        $cssFile->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('New file content'));

        $this->assertEquals($expectedContent, $this->_model->getCustomCssContent($theme));
    }

    public function prepareTheme()
    {
        $themeId = 15;
        $theme = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);
        $theme->setThemeId($themeId);

        return array(array($theme));
    }
}
