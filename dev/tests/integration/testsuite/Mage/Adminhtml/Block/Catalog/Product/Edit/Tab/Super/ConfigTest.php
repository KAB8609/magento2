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

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSelectedAttributesForSimpleProductType()
    {
        Mage::register('current_product', Mage::getModel('Mage_Catalog_Model_Product'));
        /** @var $block Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config */
        $block = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config');
        $this->assertEquals(array(), $block->getSelectedAttributes());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Catalog/_files/product_configurable.php
     */
    public function testGetSelectedAttributesForConfigurableProductType()
    {
        Mage::register('current_product', Mage::getModel('Mage_Catalog_Model_Product')->load(1));
        Mage::app()->getLayout()->createBlock('Mage_Core_Block_Text', 'head');
        $usedAttribute = Mage::getSingleton('Mage_Catalog_Model_Entity_Attribute')->loadByCode(
            Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('catalog_product')->getId(),
            'test_configurable'
        );
        /** @var $block Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config */
        $block = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config');
        $selectedAttributes = $block->getSelectedAttributes();
        $this->assertEquals(array($usedAttribute->getId()), array_keys($selectedAttributes));
        $selectedAttribute = reset($selectedAttributes);
        $this->assertEquals('test_configurable', $selectedAttribute->getAttributeCode());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Catalog/_files/product_configurable.php
     */
    public function testGetVariations()
    {
        Mage::register('current_product', Mage::getModel('Mage_Catalog_Model_Product')->load(1));
        Mage::app()->getLayout()->createBlock('Mage_Core_Block_Text', 'head');
        /** @var $usedAttribute Mage_Catalog_Model_Entity_Attribute */
        $usedAttribute = Mage::getSingleton('Mage_Catalog_Model_Entity_Attribute')->loadByCode(
            Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('catalog_product')->getId(),
            'test_configurable'
        );
        $attributeOptions = $usedAttribute->getSource()->getAllOptions(false);
        /** @var $block Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config */
        $block = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config');
        $this->assertEquals(
            array(
                array($usedAttribute->getId() => $attributeOptions[0]),
                array($usedAttribute->getId() => $attributeOptions[1]),
            ),
            $block->getVariations()
        );
    }
}
