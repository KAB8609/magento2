<?php
/**
 * Magento_Webhook_Block_Adminhtml_Subscription
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Block_Adminhtml_SubscriptionTest extends Magento_Test_Block_Adminhtml
{
    public function testConstruct()
    {
        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $block = new Magento_Webhook_Block_Adminhtml_Subscription($this->_context, $coreStoreConfig);
        $this->assertEquals('Subscriptions', $block->getHeaderText());
        $this->assertEquals('Add Subscription', $block->getAddButtonLabel());
    }
}
