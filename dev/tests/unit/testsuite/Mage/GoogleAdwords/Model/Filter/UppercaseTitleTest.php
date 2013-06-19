<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_GoogleAdwords_Model_Filter_UppercaseTitleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_GoogleAdwords_Model_Filter_UppercaseTitle
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_GoogleAdwords_Model_Filter_UppercaseTitle();
    }

    public function dataProviderForFilterValues()
    {
        return array(
            array('some name', 'Some Name'),
            array('test', 'Test'),
        );
    }

    /**
     * @param string $inputValue
     * @param string $returnValue
     * @dataProvider dataProviderForFilterValues
     */
    public function testFilter($inputValue, $returnValue)
    {
        $this->assertEquals($returnValue, $this->_model->filter($inputValue));
    }
}
