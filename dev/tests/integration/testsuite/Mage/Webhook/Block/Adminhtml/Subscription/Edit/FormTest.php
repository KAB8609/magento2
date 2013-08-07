<?php
/**
 * Mage_Webhook_Block_AdminHtml_Subscription_Edit_Form
 *
 * @magentoAppArea adminhtml
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Subscription_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareForm()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getObjectManager()->create('Mage_Core_Model_Layout');

        /** @var Mage_Core_Model_Registry $registry */
        $registry = Mage::getObjectManager()->create('Mage_Core_Model_Registry');
        $subscription = array(
            'name' => 'subscriptionName',
            'endpoint_url' => 'example.url.com',
            'format' => 'JSON',
            'authentication_type' => 'manual',
            'version' => '1.0',
            'topics' => 'customer/created'
        );
        $registry
            ->register(
                'current_subscription',
                $subscription
            );

        /** @var Mage_Webhook_Block_Adminhtml_Subscription_Edit_Form $block */
        $block = $layout->createBlock('Mage_Webhook_Block_Adminhtml_Subscription_Edit_Form',
            '', array('registry' => $registry)
        );
        $block->toHtml();

        $form = $block->getForm();

        $this->assertInstanceOf('Magento_Data_Form', $form);
        $this->assertEquals('post', $form->getData('method'));
        $this->assertEquals($block->getUrl('*/*/save'), $form->getData('action'));
        $this->assertEquals('edit_form', $form->getId());
        $this->assertTrue($form->getUseContainer());

        $expectedFieldset = array(
            'name' => array(
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'value' => $subscription['name']
            ),
            'endpoint_url' => array(
                'name' => 'endpoint_url',
                'type' => 'text',
                'required' => true,
                'value' => $subscription['endpoint_url']
            ),
            'format' => array(
                'name' => 'format',
                'type' => 'select',
                'required' => false,
                'value' => $subscription['format']
            ),
            'authentication_type' => array(
                'name' => 'authentication_type',
                'type' => 'select',
                'required' => false,
                'value' => $subscription['authentication_type']
            ),
            'version' => array(
                'name' => 'version',
                'type' => 'text',
                'required' => false,
                'value' => $subscription['version']
            ),
            'topics' => array(
                'name' => 'topics[]',
                'type' => 'select',
                'required' => true,
                'value' => $subscription['topics']
            ),
        );

        foreach ($expectedFieldset as $fieldId => $field) {
            $element = $form->getElement($fieldId);
            $this->assertInstanceOf('Magento_Data_Form_Element_Abstract', $element);
            $this->assertEquals($field['name'], $element->getName(), 'Wrong \'' . $fieldId . '\' field name');
            $this->assertEquals($field['type'], $element->getType(), 'Wrong \'' . $fieldId . ' field type');
            $this->assertEquals(
                $field['required'],
                $element->getData('required'),
                'Wrong \'' . $fieldId . '\' requirement state'
            );
            if (array_key_exists('value', $field)) {
                $this->assertEquals($field['value'], $element->getData('value'), 'Wrong \'' . $fieldId . '\' value');
            }
        }
    }
}
