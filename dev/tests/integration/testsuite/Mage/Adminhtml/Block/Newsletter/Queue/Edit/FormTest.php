<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form.
 *
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Queue_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareForm()
    {
        $block = new Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form;
        $prepareFormMethod = new ReflectionMethod(
            'Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form', '_prepareForm');
        $prepareFormMethod->setAccessible(true);

        $queue = Mage::getSingleton('Mage_Newsletter_Model_Queue');
        $statuses = array(Mage_Newsletter_Model_Queue::STATUS_NEVER, Mage_Newsletter_Model_Queue::STATUS_PAUSE);
        foreach ($statuses as $status) {
            $queue->setQueueStatus($status);
            $prepareFormMethod->invoke($block);
            $element = $block->getForm()->getElement('date');
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getTimeFormat());
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
