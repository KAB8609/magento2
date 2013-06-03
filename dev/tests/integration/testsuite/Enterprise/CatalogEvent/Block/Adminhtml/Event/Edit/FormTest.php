<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CatalogEvent
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form
 */
class Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_FormTest extends Mage_Backend_Area_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        /** @var $event Enterprise_CatalogEvent_Model_Event */
        $event = Mage::getModel('Enterprise_CatalogEvent_Model_Event');
        $event->setCategoryId(1)->setId(1);
        Mage::register('enterprise_catalogevent_event', $event);
        $block = Mage::app()->getLayout()->createBlock('Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form');
        $prepareFormMethod = new ReflectionMethod(
            'Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('date_start', 'date_end') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
            $this->assertNotEmpty($element->getTimeFormat());
        }
    }
}
