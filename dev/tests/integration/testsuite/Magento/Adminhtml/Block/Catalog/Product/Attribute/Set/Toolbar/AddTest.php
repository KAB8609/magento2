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

namespace Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Toolbar;

/**
 * @magentoAppArea adminhtml
 */
class AddTest extends \PHPUnit_Framework_TestCase
{
    public function testToHtmlFormId()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');

        $block = $layout->addBlock('Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Toolbar\Add', 'block');
        $block->setArea('adminhtml')->unsetChild('setForm');

        $childBlock = $layout->addBlock('Magento\Core\Block\Template', 'setForm', 'block');
        $form = new \Magento\Object();
        $childBlock->setForm($form);

        $expectedId = '12121212';
        $this->assertNotContains($expectedId, $block->toHtml());
        $form->setId($expectedId);
        $this->assertContains($expectedId, $block->toHtml());
    }
}
