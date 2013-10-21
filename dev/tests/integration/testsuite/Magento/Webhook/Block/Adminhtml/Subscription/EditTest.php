<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Subscription;

/**
 * \Magento\Webhook\Block\Adminhtml\Subscription\Edit
 *
 * @magentoAppArea adminhtml
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Model\Registry */
    private $_registry;

    protected function setUp()
    {
        $this->_registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Registry');
    }

    protected function tearDown()
    {
        $this->_registry->unregister('current_subscription');
    }

    public function testAddSubscriptionTitle()
    {
        /** @var \Magento\View\Layout $layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\View\Layout');

        $subscription = array(
            'subscription_id' => null,
        );
        $this->_registry->register('current_subscription', $subscription);

        /** @var \Magento\Webhook\Block\Adminhtml\Subscription\Edit $block */
        $block = $layout->createBlock('Magento\Webhook\Block\Adminhtml\Subscription\Edit',
            '', array('registry' => $this->_registry)
        );
        $block->toHtml();
        $this->assertEquals('Add Subscription', $block->getHeaderText());

    }

    public function testEditSubscriptionTitle()
    {
        /** @var \Magento\View\Layout $layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\View\Layout');

        $subscription = array(
            'subscription_id' => 1,
        );
        $this->_registry->register('current_subscription', $subscription);

        /** @var \Magento\Webhook\Block\Adminhtml\Subscription\Edit $block */
        $block = $layout->createBlock('Magento\Webhook\Block\Adminhtml\Subscription\Edit',
            '', array('registry' => $this->_registry)
        );
        $block->toHtml();
        $this->assertEquals('Edit Subscription', $block->getHeaderText());
    }
}
