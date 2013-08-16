<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test collection Enterprise_ImportExport_Model_Resource_Customer_Collection
 *
 * @magentoConfigFixture current_store enterprise_reward/general/is_enabled            1
 * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled  1
 */
class Enterprise_ImportExport_Model_Resource_Customer_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Remove not used websites
     */
    protected function tearDown()
    {
        Mage::app()->reinitStores();
    }

    /**
     * Test join with reward points
     *
     * @magentoDataFixture Enterprise/ImportExport/_files/customer_finance.php
     */
    public function testJoinWithRewardPoints()
    {
        /** @var $collection Enterprise_ImportExport_Model_Resource_Customer_Collection */
        $collection = Mage::getModel('Enterprise_ImportExport_Model_Resource_Customer_Collection');
        $collection->joinWithRewardPoints();
        $items = $collection->getItems();
        $this->assertCount(1, $items);

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = reset($items);
        /** @var $website Mage_Core_Model_Website */
        foreach (Mage::app()->getWebsites() as $website) {
            $key = $website->getCode() . '_'
                . Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_REWARD_POINTS;
            $rewardPoints = $customer->getData($key);
            $this->assertNotEmpty($rewardPoints);
            $this->assertEquals(Mage::registry('reward_point_balance_' . $website->getCode()), $rewardPoints);
        }
    }

    /**
     * Test join with customer balance
     *
     * @magentoDataFixture Enterprise/ImportExport/_files/customer_finance.php
     */
    public function testJoinWithCustomerBalance()
    {
        /** @var $collection Enterprise_ImportExport_Model_Resource_Customer_Collection */
        $collection = Mage::getModel('Enterprise_ImportExport_Model_Resource_Customer_Collection');
        $collection->joinWithCustomerBalance();
        $items = $collection->getItems();
        $this->assertCount(1, $items);

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = reset($items);
        /** @var $website Mage_Core_Model_Website */
        foreach (Mage::app()->getWebsites() as $website) {
            $key = $website->getCode() . '_'
                . Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_CUSTOMER_BALANCE;
            $customerBalance = $customer->getData($key);
            $this->assertNotEmpty($customerBalance);
            $this->assertEquals(Mage::registry('customer_balance_' . $website->getCode()), $customerBalance);
        }
    }

    /**
     * Test filter with reward points and customer balance
     *
     * @magentoDataFixture Enterprise/ImportExport/_files/customer_finance_all_cases.php
     */
    public function testFilterWithRewardPointsAndCustomerBalance()
    {
        /** @var $collection Enterprise_ImportExport_Model_Resource_Customer_Collection */
        $collection = Mage::getModel('Enterprise_ImportExport_Model_Resource_Customer_Collection');
        $collection->joinWithCustomerBalance()
            ->joinWithRewardPoints();
        $items = $collection->getItems();
        $this->assertCount(3, $items);

        $emails = array();
        foreach ($items as $item) {
            $emails[] = $item->getEmail();
        }

        $this->assertContains(Mage::registry('customer_finance_email_rp_cb'), $emails);
        $this->assertContains(Mage::registry('customer_finance_email_rp'), $emails);
        $this->assertContains(Mage::registry('customer_finance_email_cb'), $emails);
        $this->assertNotContains(Mage::registry('customer_finance_email'), $emails);
    }

    /**
     * Test filter only with reward points
     *
     * @magentoDataFixture Enterprise/ImportExport/_files/customer_finance_all_cases.php
     */
    public function testFilterWithRewardPoints()
    {
        /** @var $collection Enterprise_ImportExport_Model_Resource_Customer_Collection */
        $collection = Mage::getModel('Enterprise_ImportExport_Model_Resource_Customer_Collection');
        $collection->joinWithRewardPoints();
        $items = $collection->getItems();
        $this->assertCount(2, $items);

        $emails = array();
        foreach ($items as $item) {
            $emails[] = $item->getEmail();
        }

        $this->assertContains(Mage::registry('customer_finance_email_rp_cb'), $emails);
        $this->assertContains(Mage::registry('customer_finance_email_rp'), $emails);
        $this->assertNotContains(Mage::registry('customer_finance_email_cb'), $emails);
        $this->assertNotContains(Mage::registry('customer_finance_email'), $emails);
    }

    /**
     * Test filter only with customer balance
     *
     * @magentoDataFixture Enterprise/ImportExport/_files/customer_finance_all_cases.php
     */
    public function testFilterWithCustomerBalance()
    {
        /** @var $collection Enterprise_ImportExport_Model_Resource_Customer_Collection */
        $collection = Mage::getModel('Enterprise_ImportExport_Model_Resource_Customer_Collection');
        $collection->joinWithCustomerBalance();
        $items = $collection->getItems();
        $this->assertCount(2, $items);

        $emails = array();
        foreach ($items as $item) {
            $emails[] = $item->getEmail();
        }

        $this->assertContains(Mage::registry('customer_finance_email_rp_cb'), $emails);
        $this->assertNotContains(Mage::registry('customer_finance_email_rp'), $emails);
        $this->assertContains(Mage::registry('customer_finance_email_cb'), $emails);
        $this->assertNotContains(Mage::registry('customer_finance_email'), $emails);
    }

    /**
     * Test filter only without customer balance and reward points
     *
     * @magentoDataFixture Enterprise/ImportExport/_files/customer_finance_all_cases.php
     */
    public function testFilterWithoutRewardPointsAndCustomerBalance()
    {
        /** @var $collection Enterprise_ImportExport_Model_Resource_Customer_Collection */
        $collection = Mage::getModel('Enterprise_ImportExport_Model_Resource_Customer_Collection');
        $items = $collection->getItems();
        $this->assertCount(4, $items);

        $emails = array();
        foreach ($items as $item) {
            $emails[] = $item->getEmail();
        }

        $this->assertContains(Mage::registry('customer_finance_email_rp_cb'), $emails);
        $this->assertContains(Mage::registry('customer_finance_email_rp'), $emails);
        $this->assertContains(Mage::registry('customer_finance_email_cb'), $emails);
        $this->assertContains(Mage::registry('customer_finance_email'), $emails);
    }
}
