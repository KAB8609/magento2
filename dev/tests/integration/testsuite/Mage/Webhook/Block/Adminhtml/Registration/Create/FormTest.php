<?php
/**
 * Mage_Webhook_Block_Adminhtml_Registration_Create_Form
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
class Mage_Webhook_Block_Adminhtml_Registration_Create_FormTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareForm()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getObjectManager()->create('Mage_Core_Model_Layout');

        /** @var Mage_Core_Model_Registry $registry */
        $registry = Mage::getObjectManager()->create('Mage_Core_Model_Registry');
        $subscriptionData = array(
            'subscription_id' => '333',
        );
        $registry
            ->register(
                'current_subscription',
                $subscriptionData
            );

        /** @var Mage_Webhook_Block_Adminhtml_Registration_Create_Form $block */
        $block = $layout->createBlock('Mage_Webhook_Block_Adminhtml_Registration_Create_Form',
            '', array('registry' => $registry)
        );
        $block->toHtml();

        $form = $block->getForm();

        $this->assertInstanceOf('Magento_Data_Form', $form);
        $this->assertEquals('post', $form->getData('method'));
        $this->assertEquals($block->getUrl('*/*/register', array('id' => 333)), $form->getData('action'));
        $this->assertEquals('api_user', $form->getId());


        $expectedFieldset = array(
            'company' => array(
                'name' => 'company',
                'type' => 'text',
                'required' => false
            ),
            'email' => array(
                'name' => 'email',
                'type' => 'text',
                'required' => true
            ),
            'apikey' => array(
                'name' => 'apikey',
                'type' => 'text',
                'required' => true
            ),
            'apisecret' => array(
                'name' => 'apisecret',
                'type' => 'text',
                'required' => true
            )
        );

        foreach ($expectedFieldset as $fieldId => $field) {
            $element = $form->getElement($fieldId);
            $this->assertInstanceOf('Magento_Data_Form_Element_Abstract', $element);
            $this->assertEquals($field['name'], $element->getName(), 'Wrong \'' . $fieldId . '\' field name');
            $this->assertEquals($field['type'], $element->getType(), 'Wrong \'' . $fieldId . ' field type');
            $this->assertEquals($field['required'], $element->getData('required'),
                'Wrong \'' . $fieldId . '\' requirement state'
            );
        }
    }
}