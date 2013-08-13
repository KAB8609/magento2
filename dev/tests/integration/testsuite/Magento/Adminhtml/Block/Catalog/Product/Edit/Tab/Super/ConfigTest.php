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
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSelectedAttributesForSimpleProductType()
    {
        Mage::register('current_product', Mage::getModel('Mage_Catalog_Model_Product'));
        /** @var $block Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config */
        $block = Mage::app()->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config');
        $this->assertEquals(array(), $block->getSelectedAttributes());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Catalog/_files/product_configurable.php
     */
    public function testGetSelectedAttributesForConfigurableProductType()
    {
        Mage::register('current_product', Mage::getModel('Mage_Catalog_Model_Product')->load(1));
        Mage::app()->getLayout()->createBlock('Magento_Core_Block_Text', 'head');
        $usedAttribute = Mage::getSingleton('Mage_Catalog_Model_Entity_Attribute')->loadByCode(
            Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('catalog_product')->getId(),
            'test_configurable'
        );
        /** @var $block Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config */
        $block = Mage::app()->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config');
        $selectedAttributes = $block->getSelectedAttributes();
        $this->assertEquals(array($usedAttribute->getId()), array_keys($selectedAttributes));
        $selectedAttribute = reset($selectedAttributes);
        $this->assertEquals('test_configurable', $selectedAttribute->getAttributeCode());
    }
}
