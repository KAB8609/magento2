<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Eav_Model_Attribute_Data_TextTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Eav_Model_Attribute_Data_Text
     */
    protected $_model;

    protected function setUp()
    {
        $helper = $this->getMock('Mage_Core_Helper_String', array('__'));
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0))
        ;
        $attributeData = array(
            'store_label' => 'Test',
            'attribute_code' => 'test',
            'is_required' => 1,
            'validate_rules' => array(
                'min_text_length' => 0,
                'max_text_length' => 0,
                'input_validation' => 0
            )
        );
        /** @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract|PHPUnit_Framework_MockObject_MockObject */
        $attribute = $this->getMock(
            'Mage_Eav_Model_Entity_Attribute_Abstract', array('_init'), array($attributeData)
        );
        $this->_model = new Mage_Eav_Model_Attribute_Data_Text(array(
            'translationHelper' => $helper,
            'stringHelper' => $helper,
        ));
        $this->_model->setAttribute($attribute);
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @dataProvider validateValueDataProvider
     * @param mixed $inputValue
     * @param mixed $expectedResult
     */
    public function testValidateValue($inputValue, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_model->validateValue($inputValue));
    }

    public static function validateValueDataProvider()
    {
        return array(
            'zero string'  => array('0', true),
            'zero integer' => array(0, array('"%s" is a required value.'))
        );
    }
}