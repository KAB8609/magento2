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
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_SelectTest extends PHPUnit_Framework_TestCase
{
    public function testToHtmlFormId()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getModel('Magento\Core\Model\Layout');
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Type\Select */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Type\Select', 'select');
        $html = $block->getPriceTypeSelectHtml();
        $this->assertContains('select_${select_id}', $html);
        $this->assertContains('[${select_id}]', $html);
    }
}
