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
namespace Magento\Adminhtml\Block\Page;

class HeadTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento\Adminhtml\Block\Page\Head',
            \Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Page\Head')
        );
    }
}
