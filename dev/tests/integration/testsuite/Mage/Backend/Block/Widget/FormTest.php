<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Block_Widget_Form
 * @magentoAppArea adminhtml
 */
class Mage_Backend_Block_Widget_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testSetFieldset()
    {
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        $layout = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Core_Model_Layout');
        $formBlock = $layout->addBlock('Mage_Backend_Block_Widget_Form');
        $fieldSet = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Data_Form_Element_Fieldset');
        $arguments = array(
            'data' => array(
                'attribute_code' => 'date',
                'backend_type'   => 'datetime',
                'frontend_input' => 'date',
                'frontend_label' => 'Date',
            )
        );
        $attributes = array(Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Eav_Model_Entity_Attribute', $arguments));
        $method = new ReflectionMethod('Mage_Backend_Block_Widget_Form', '_setFieldset');
        $method->setAccessible(true);
        $method->invoke($formBlock, $attributes, $fieldSet);
        $fields = $fieldSet->getElements();

        $this->assertEquals(1, count($fields));
        $this->assertInstanceOf('Magento_Data_Form_Element_Date', $fields[0]);
        $this->assertNotEmpty($fields[0]->getDateFormat());
    }
}
