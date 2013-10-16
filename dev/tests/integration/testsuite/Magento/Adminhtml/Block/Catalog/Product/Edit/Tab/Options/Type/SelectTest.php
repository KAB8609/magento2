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

namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Type;

/**
 * @magentoAppArea adminhtml
 */
class SelectTest extends \PHPUnit_Framework_TestCase
{
    public function testToHtmlFormId()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\Layout');
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Type_Select */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Type\Select', 'select');
        $html = $block->getPriceTypeSelectHtml();
        $this->assertContains('select_${select_id}', $html);
        $this->assertContains('[${select_id}]', $html);
    }
}
