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

class Mage_Core_Model_Design_Fallback_Rule_CompositeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Each item should implement the fallback rule interface
     */
    public function testConstructException()
    {
        new Mage_Core_Model_Design_Fallback_Rule_Composite(array(new stdClass));
    }

    public function testGetPatternDirs()
    {
        $inputParams = array('param_one' => 'value_one', 'param_two' => 'value_two');

        $ruleOne = $this->getMockForAbstractClass('Mage_Core_Model_Design_Fallback_Rule_RuleInterface');
        $ruleOne
            ->expects($this->once())
            ->method('getPatternDirs')
            ->with($inputParams)
            ->will($this->returnValue(array('rule_one/path/one', 'rule_one/path/two')))
        ;

        $ruleTwo = $this->getMockForAbstractClass('Mage_Core_Model_Design_Fallback_Rule_RuleInterface');
        $ruleTwo
            ->expects($this->once())
            ->method('getPatternDirs')
            ->with($inputParams)
            ->will($this->returnValue(array('rule_two/path/one', 'rule_two/path/two')))
        ;

        $object = new Mage_Core_Model_Design_Fallback_Rule_Composite(array($ruleOne, $ruleTwo));

        $expectedResult = array(
            'rule_one/path/one',
            'rule_one/path/two',
            'rule_two/path/one',
            'rule_two/path/two',
        );
        $this->assertEquals($expectedResult, $object->getPatternDirs($inputParams));
    }
}
