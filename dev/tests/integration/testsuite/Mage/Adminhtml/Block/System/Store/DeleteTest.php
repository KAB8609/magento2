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

class Mage_Adminhtml_Block_System_Store_DeleteTest extends PHPUnit_Framework_TestCase
{
    public function testGetHeaderText()
    {
        $layout = new Mage_Core_Model_Layout();
        $block = $layout->createBlock('Mage_Adminhtml_Block_System_Store_Delete', 'block');

        $dataObject = new Varien_Object;
        $form = $block->getChildBlock('form');
        $form->setDataObject($dataObject);

        $expectedValue = 'header_text_test';
        $this->assertNotContains($expectedValue, $block->getHeaderText());

        $dataObject->setName($expectedValue);
        $this->assertContains($expectedValue, $block->getHeaderText());
    }
}
