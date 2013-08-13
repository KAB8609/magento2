<?php
/**
 * Mage_Webhook_Block_Adminhtml_Subscription
 *
 * @magentoAppArea adminhtml
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_SubscriptionTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_ObjectManager */
    private $_objectManager;

    public function testConstruct()
    {
        $this->_objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $block = $this->_objectManager->create('Mage_Webhook_Block_Adminhtml_Subscription');
        $this->assertEquals('Subscriptions', $block->getHeaderText());
        $this->assertEquals('Add Subscription', $block->getAddButtonLabel());
    }
}