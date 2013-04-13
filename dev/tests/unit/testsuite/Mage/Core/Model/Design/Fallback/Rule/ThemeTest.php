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

class Mage_Core_Model_Design_Fallback_Rule_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Parameter "theme" should be specified and should implement the theme interface
     */
    public function testGetPatternDirsException()
    {
        $rule = $this->getMockForAbstractClass('Mage_Core_Model_Design_Fallback_Rule_RuleInterface');
        $object = new Mage_Core_Model_Design_Fallback_Rule_Theme($rule);
        $object->getPatternDirs(array());
    }

    public function testGetPatternDirs()
    {
        $parentTheme = $this->getMockForAbstractClass('Mage_Core_Model_ThemeInterface');
        $parentTheme->expects($this->any())->method('getThemePath')->will($this->returnValue('package/parent_theme'));

        $theme = $this->getMockForAbstractClass('Mage_Core_Model_ThemeInterface');
        $theme->expects($this->any())->method('getThemePath')->will($this->returnValue('package/current_theme'));
        $theme->expects($this->any())->method('getParentTheme')->will($this->returnValue($parentTheme));

        $ruleDirsMap = array(
            array(
                array('theme_path' => 'package/current_theme'),
                array('package/current_theme/path/one', 'package/current_theme/path/two')
            ),
            array(
                array('theme_path' => 'package/parent_theme'),
                array('package/parent_theme/path/one', 'package/parent_theme/path/two')
            )
        );
        $rule = $this->getMockForAbstractClass('Mage_Core_Model_Design_Fallback_Rule_RuleInterface');
        $rule->expects($this->any())->method('getPatternDirs')->will($this->returnValueMap($ruleDirsMap));

        $object = new Mage_Core_Model_Design_Fallback_Rule_Theme($rule);

        $expectedResult = array(
            'package/current_theme/path/one',
            'package/current_theme/path/two',
            'package/parent_theme/path/one',
            'package/parent_theme/path/two',
        );
        $this->assertEquals($expectedResult, $object->getPatternDirs(array('theme' => $theme)));
    }
}
