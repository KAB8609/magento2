<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Rule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Rule_Model_Condition_Abstract
 */
class Mage_Rule_Model_Condition_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testGetValueElement()
    {
        /** @var Mage_Rule_Model_Condition_Abstract $model */
        $model = $this->getMockForAbstractClass('Mage_Rule_Model_Condition_Abstract', array(), '',
            true, true, true, array('getValueElementRenderer'));
        $model->expects($this->any())
             ->method('getValueElementRenderer')
             ->will($this->returnValue(new Mage_Rule_Block_Editable));

        $rule = new Mage_Rule_Model_Rule;
        $model->setRule($rule->setForm(new Varien_Data_Form()));

        $property = new ReflectionProperty('Mage_Rule_Model_Condition_Abstract', '_inputType');
        $property->setAccessible(true);
        $property->setValue($model, 'date');

        $element = $model->getValueElement();
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
