<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Product\View;

class TabsTest extends \PHPUnit_Framework_TestCase
{
    public function testAddTab()
    {
        $tabBlock = $this->getMock('Magento\View\Block\Template', array(), array(), '', false);
        $tabBlock->expects($this->once())
            ->method('setTemplate')
            ->with('template')
            ->will($this->returnSelf());

        $layout = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $layout->expects($this->once())
            ->method('createBlock')
            ->with('block')
            ->will($this->returnValue($tabBlock));

        $context = $this->getMock('Magento\Core\Block\Template\Context', array(), array(), '', false);
        $context->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $coreData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);

        $block = new \Magento\Catalog\Block\Product\View\Tabs($coreData, $context);
        $block->addTab('alias', 'title', 'block', 'template', 'header');

        $expectedTabs = array(
            array('alias' => 'alias', 'title' => 'title', 'header' => 'header')
        );
        $this->assertEquals($expectedTabs, $block->getTabs());
    }
}
