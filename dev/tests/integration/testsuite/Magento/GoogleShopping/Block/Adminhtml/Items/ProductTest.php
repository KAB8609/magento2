<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_GoogleShopping_Block_Adminhtml_Items_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testBeforeToHtml()
    {
        $this->markTestIncomplete('Magento_GoogleShopping is not implemented yet');

        $block  = Mage::app()->getLayout()->createBlock('Magento_GoogleShopping_Block_Adminhtml_Items_Product');
        $filter = Mage::app()->getLayout()->createBlock('Magento_Core_Block_Text');
        $search = Mage::app()->getLayout()->createBlock('Magento_Core_Block_Text');

        $layout = Mage::getModel('Magento_Core_Model_Layout');
        $layout->addBlock($block, 'product');
        $layout->addBlock($filter, 'reset_filter_button', 'product');
        $layout->addBlock($search, 'search_button', 'product');
        $block->toHtml();

        $this->assertEquals('googleshopping_selection_search_grid_JsObject.resetFilter()', $filter->getData('onclick'));
        $this->assertEquals('googleshopping_selection_search_grid_JsObject.doFilter()', $search->getData('onclick'));
    }
}