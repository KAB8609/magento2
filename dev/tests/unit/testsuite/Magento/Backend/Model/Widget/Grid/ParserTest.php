<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Widget_Grid_ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Widget_Grid_Parser
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_Backend_Model_Widget_Grid_Parser();
    }

    /**
     * @param string $expression
     * @param array $expected
     * @dataProvider parseExpressionDataProvider
     */
    public function testParseExpression($expression, $expected)
    {
        $this->assertEquals($expected, $this->_model->parseExpression($expression));
    }

    /**
     * @return array
     */
    public function parseExpressionDataProvider()
    {
        return array(
            array(
                '1-2',
                array('1', '2', '-')
            ),
            array(
                '1*2',
                array('1', '2', '*')
            ),
            array(
                '1/2',
                array('1', '2', '/')
            ),
            array(
                '1+2+3',
                array('1', '2', '+', '3', '+')
            ),
            array(
                '1*2*3+4',
                array('1', '2', '*', '3', '*', '4', '+')
            ),
            array(
                '1-2-3',
                array('1', '2', '-', '3', '-')
            ),
            array(
                '1*2*3',
                array('1', '2', '*', '3', '*')
            ),
            array(
                '1/2/3',
                array('1', '2', '/', '3', '/')
            ),
            array(
                '1 * 2 / 3 + 4 * 5 * 6 - 7 - 8',
                array('1', '2', '*', '3', '/', '4', '5', '*', '6', '*', '+', '7', '-', '8', '-')
            ),
        );
    }

    /**
     * @param $operation
     * @param $expected
     * @dataProvider isOperationDataProvider
     */
    public function testIsOperation($operation, $expected)
    {
        $this->assertEquals($expected, $this->_model->isOperation($operation));
    }

    public function isOperationDataProvider()
    {
        return array(
            array('+', true),
            array('-', true),
            array('*', true),
            array('/', true),
            array('0', false),
            array('aa', false)
        );
    }
}
