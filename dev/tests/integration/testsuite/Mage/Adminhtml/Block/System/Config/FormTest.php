<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_System_Config_FormTest extends PHPUnit_Framework_TestCase
{
    public function testDependenceHtml()
    {
        $layout = new Mage_Core_Model_Layout();
        $block = $layout->createBlock('Mage_Adminhtml_Block_System_Config_Form', 'block');
        $block->setArea('adminhtml');

        $childBlock = $layout->addBlock('Mage_Core_Block_Text', 'element_dependence', 'block');

        $expectedValue = 'dependence_html_relations';
        $this->assertNotContains($expectedValue, $block->toHtml());

        $childBlock->setText($expectedValue);
        $this->assertContains($expectedValue, $block->toHtml());
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Config_Form::initFields
     * @param $section Mage_Core_Model_Config_Element
     * @param $group Mage_Core_Model_Config_Element
     * @param $field Mage_Core_Model_Config_Element
     * @param array $configData
     * @param bool $expectedUseDefault
     * @dataProvider initFieldsInheritCheckboxDataProvider
     */
    public function testInitFieldsUseDefaultCheckbox($section, $group, $field, array $configData, $expectedUseDefault)
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset($section->getName() . '_' . $group->getName(), array());

        $block = new Mage_Adminhtml_Block_System_Config_FormStub();
        $block->setScope(Mage_Adminhtml_Block_System_Config_Form::SCOPE_WEBSITES);
        $block->setStubConfigData($configData);
        $block->initFields($fieldset, $group, $section);

        $fieldsetPath = 'fieldset';
        $valuePath = sprintf('input#%s_%s_%s', $section->getName(), $group->getName(), $field->getName());
        $valueDisabledPath = sprintf('%s[disabled="disabled"]', $valuePath);
        $checkboxPath = sprintf('input#%s_%s_%s_inherit.checkbox', $section->getName(), $group->getName(),
            $field->getName());
        $checkboxCheckedPath = sprintf('%s[checked="checked"]', $checkboxPath);
        $fieldsetHtml = $fieldset->getElementHtml();

        $this->assertSelectCount($fieldsetPath, true, $fieldsetHtml, 'Fieldset HTML is invalid');
        $this->assertSelectCount($valuePath, true, $fieldsetHtml, 'Field input not found in fieldset HTML');
        $this->assertSelectCount($checkboxPath, true, $fieldsetHtml,
            '"Use Default" checkbox not found in fieldset HTML');

        if ($expectedUseDefault) {
            $this->assertSelectCount($checkboxCheckedPath, true, $fieldsetHtml,
                '"Use Default" checkbox should be checked');
            $this->assertSelectCount($valueDisabledPath, true, $fieldsetHtml,
                'Field input should be disabled');
        } else {
            $this->assertSelectCount($checkboxCheckedPath, false, $fieldsetHtml,
                '"Use Default" checkbox should not be checked');
            $this->assertSelectCount($valueDisabledPath, false, $fieldsetHtml,
                'Field input should not be disabled');
        }
    }

    /**
     * @return array
     */
    public function initFieldsInheritCheckboxDataProvider()
    {
        $section = new Mage_Core_Model_Config_Element(file_get_contents(__DIR__ . '/_files/test_section_config.xml'));
        $group = current($section->xpath('groups/test_group'));
        $field = current($group->xpath('fields/test_field'));
        $fieldPath = (string)current($field->xpath('config_path'));

        return array(
            array($section, $group, $field, array(), true),
            array($section, $group, $field, array($fieldPath => null), false),
            array($section, $group, $field, array($fieldPath => ''), false),
            array($section, $group, $field, array($fieldPath => 'value'), false),
        );
    }
}
