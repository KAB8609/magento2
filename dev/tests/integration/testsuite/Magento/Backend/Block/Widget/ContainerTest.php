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
class Magento_Backend_Block_Widget_ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testPseudoConstruct()
    {
        /** @var $block \Magento\Backend\Block\Widget\Container */
        $block = Mage::app()->getLayout()->createBlock('\Magento\Backend\Block\Widget\Container', '',
            array('data' => array(
                \Magento\Backend\Block\Widget\Container::PARAM_CONTROLLER => 'one',
                \Magento\Backend\Block\Widget\Container::PARAM_HEADER_TEXT => 'two',
            ))
        );
        $this->assertStringEndsWith('one', $block->getHeaderCssClass());
        $this->assertContains('two', $block->getHeaderText());
    }
}
