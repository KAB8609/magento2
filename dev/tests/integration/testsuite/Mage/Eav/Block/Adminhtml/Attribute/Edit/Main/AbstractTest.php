<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Eav
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
 */
class Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Core/_files/init_adminhtml_design.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $entityType = Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('customer');
        $model = Mage::getObjectManager()->create('Mage_Customer_Model_Attribute');
        $model->setEntityTypeId($entityType->getId());
        Mage::register('entity_attribute', $model);

        $block = $this->getMockForAbstractClass(
            'Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract',
            array(Mage::getSingleton('Mage_Core_Block_Template_Context'))
        )
        ->setLayout(Mage::getObjectManager()->create('Mage_Core_Model_Layout'));

        $method = new ReflectionMethod(
            'Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract', '_prepareForm');
        $method->setAccessible(true);
        $method->invoke($block);

        $element = $block->getForm()->getElement('default_value_date');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
