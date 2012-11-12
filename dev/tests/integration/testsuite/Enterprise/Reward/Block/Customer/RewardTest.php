<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Reward
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Reward_Block_Customer_RewardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Enterprise/Reward/_files/history.php
     */
    public function testToHtml()
    {
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->load(1);
        Mage::getSingleton('Mage_Customer_Model_Session')->setCustomer($customer);
        $utility = new Mage_Core_Utility_Layout($this);
        $layout = $utility->getLayoutFromFixture(__DIR__ . '/../../_files/customer_info.xml',
            $utility->getLayoutDependencies());
        $layout->getUpdate()->addHandle('enterprise_reward_customer_info')->load();
        $layout->generateXml()->generateElements();
        $layout->addOutputElement('customer.reward');

        $format = '%A<div class="box info-box">%A<table id="reward-history" class="data-table">%A'
            . 'id="subscribe_updates"%A';
        $this->assertStringMatchesFormat($format, $layout->getOutput());
    }
}
