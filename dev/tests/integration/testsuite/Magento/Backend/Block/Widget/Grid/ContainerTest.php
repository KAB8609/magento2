<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Backend\Block\Widget\Grid;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testPseudoConstruct()
    {
        /** @var $block \Magento\Backend\Block\Widget\Grid\Container */
        $block = \Mage::app()->getLayout()->createBlock('Magento\Backend\Block\Widget\Grid\Container', '', array(
            'data' => array(
                \Magento\Backend\Block\Widget\Container::PARAM_CONTROLLER => 'widget',
                \Magento\Backend\Block\Widget\Container::PARAM_HEADER_TEXT => 'two',
                \Magento\Backend\Block\Widget\Grid\Container::PARAM_BLOCK_GROUP => 'Magento_Backend',
                \Magento\Backend\Block\Widget\Grid\Container::PARAM_BUTTON_NEW => 'four',
                \Magento\Backend\Block\Widget\Grid\Container::PARAM_BUTTON_BACK => 'five',
            )
        ));
        $this->assertStringEndsWith('widget', $block->getHeaderCssClass());
        $this->assertContains('two', $block->getHeaderText());
        $this->assertInstanceOf('Magento\Backend\Block\Widget\Grid', $block->getChildBlock('grid'));
        $this->assertEquals('four', $block->getAddButtonLabel());
        $this->assertEquals('five', $block->getBackButtonLabel());
    }
}
