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


namespace Magento\Adminhtml\Block\Customer;

/**
 * @magentoAppArea adminhtml
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetFilterFormHtml()
    {
        /** @var $layout \Magento\View\LayoutInterface */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Layout',
            array('area' => \Magento\Core\Model\App\Area::AREA_ADMINHTML)
        );
        /** @var $block \Magento\Adminhtml\Block\Customer\Online */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Customer\Online', 'block');
        $this->assertNotEmpty($block->getFilterFormHtml());
    }
}
