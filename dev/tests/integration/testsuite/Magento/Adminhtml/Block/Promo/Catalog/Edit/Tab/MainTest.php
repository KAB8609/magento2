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

/**
 * Test class for Magento_Adminhtml_Block_Promo_Catalog_Edit_Tab_Main
 *
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Promo_Catalog_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $rule = $objectManager->create('Magento_CatalogRule_Model_Rule');
        $objectManager->get('Magento_Core_Model_Registry')->register('current_promo_catalog_rule', $rule);

        $block = $objectManager->create('Magento_Adminhtml_Block_Promo_Catalog_Edit_Tab_Main');
        $block->setLayout($objectManager->create('Magento_Core_Model_Layout'));
        $prepareFormMethod = new ReflectionMethod(
            'Magento_Adminhtml_Block_Promo_Catalog_Edit_Tab_Main', '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('from_date', 'to_date') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
