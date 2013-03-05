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
 * Test class for Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_MainTest extends Mage_Backend_Area_TestCase
{
    /**
     * @magentoDataFixture Mage/Core/_files/init_adminhtml_design.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::register('current_promo_quote_rule', Mage::getObjectManager()->create('Mage_SalesRule_Model_Rule'));

        $layout = Mage::getObjectManager()->create('Mage_Core_Model_Layout');
        $block = $layout->addBlock('Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main');
        $prepareFormMethod = new ReflectionMethod(
            'Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main', '_prepareForm');
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
