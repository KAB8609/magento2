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
        Mage::register('current_product', Mage::getModel('Magento\Catalog\Model\Product'));
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config */
        $block = Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config');
        $this->assertEquals(array(), $block->getSelectedAttributes());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
     */
    public function testGetSelectedAttributesForConfigurableProductType()
    {
        Mage::register('current_product', Mage::getModel('Magento\Catalog\Model\Product')->load(1));
        Mage::app()->getLayout()->createBlock('Magento\Core\Block\Text', 'head');
        $usedAttribute = Mage::getSingleton('Magento\Catalog\Model\Entity\Attribute')->loadByCode(
            Mage::getSingleton('Magento\Eav\Model\Config')->getEntityType('catalog_product')->getId(),
            'test_configurable'
        );
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config */
        $block = Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config');
        $selectedAttributes = $block->getSelectedAttributes();
        $this->assertEquals(array($usedAttribute->getId()), array_keys($selectedAttributes));
        $selectedAttribute = reset($selectedAttributes);
        $this->assertEquals('test_configurable', $selectedAttribute->getAttributeCode());
    }
}
