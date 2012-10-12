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

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetGridJsObject()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        Mage::register('current_product', new Varien_Object);
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $block = $layout->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config', 'block');
        $this->assertEquals('super_product_linksJsObject', $block->getGridJsObject());
    }
}
