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

namespace Magento\Adminhtml\Block;

/**
 * @magentoAppArea adminhtml
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf(
            'Magento\Backend\Block\Template',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
                ->createBlock('Magento\Adminhtml\Block\Template')
        );
    }
}
