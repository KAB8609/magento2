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
class Mage_Core_Model_ShellAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_ShellAbstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass(
            'Mage_Core_Model_ShellAbstract',
            array(array()),
            '',
            true,
            true,
            true,
            array('_applyPhpVariables')
        );
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param array $arguments
     * @param string $argName
     * @param string $expectedValue
     *
     * @dataProvider setGetArgDataProvider
     */
    public function testSetGetArg($arguments, $argName, $expectedValue)
    {
        $this->_model->setRawArgs($arguments);
        $this->assertEquals($this->_model->getArg($argName), $expectedValue);
    }

    /**
     * @return array
     */
    public function setGetArgDataProvider()
    {
        return array(
            'argument with no value' => array(
                'arguments' => array(
                    'argument', 'argument2'
                ),
                'argName' => 'argument',
                'expectedValue' => true
            ),
            'dashed argument with value' => array(
                'arguments' => array(
                    '-argument',
                    'value'
                ),
                'argName' => 'argument',
                'expectedValue' => 'value'
            ),
            'double-dashed argument with separate value' => array(
                'arguments' => array(
                    '--argument-name',
                    'value'
                ),
                'argName' => 'argument-name',
                'expectedValue' => 'value'
            ),
            'double-dashed argument with included value' => array(
                'arguments' => array(
                    '--argument-name=value'
                ),
                'argName' => 'argument-name',
                'expectedValue' => 'value'
            ),
            'argument with value, then single argument with no value' => array(
                'arguments' => array(
                    '-argument',
                    'value',
                    'argument2'
                ),
                'argName' => 'argument',
                'expectedValue' => 'value'
            ),
        );
    }
}
