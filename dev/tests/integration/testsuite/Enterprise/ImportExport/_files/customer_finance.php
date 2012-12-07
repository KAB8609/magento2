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

// add new website
/** @var $website Mage_Core_Model_Website */
$website = Mage::getModel('Mage_Core_Model_Website');
$website->setCode('finance_website')
    ->setName('Finance Website');
$website->save();
Mage::app()->reinitStores();

// create test customer
/** @var $customer Mage_Customer_Model_Customer */
$customer = Mage::getModel('Mage_Customer_Model_Customer');
$customer->addData(array(
    'firstname' => 'Test',
    'lastname' => 'User'
));
$customerEmail = 'customer_finance_test@test.com';
$registerKey = 'customer_finance_email';
Mage::unregister($registerKey);
Mage::register($registerKey, $customerEmail);
$customer->setEmail($customerEmail);
$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$customer->save();

// create store credit and reward points
/** @var $helper Enterprise_ImportExport_Helper_Data */
$helper = Mage::helper('Enterprise_ImportExport_Helper_Data');

// increment to modify balance values
$increment = 0;
/** @var $website Mage_Core_Model_Website */
foreach (Mage::app()->getWebsites() as $website) {
    $increment += 10;

    /** @var $customerBalance Enterprise_CustomerBalance_Model_Balance */
    $customerBalance = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');
    $customerBalance->setCustomerId($customer->getId());
    $customerBalanceAmount = 50 + $increment;
    $registerKey = 'customer_balance_' . $website->getCode();
    Mage::unregister($registerKey);
    Mage::register($registerKey, $customerBalanceAmount);
    $customerBalance->setAmountDelta($customerBalanceAmount);
    $customerBalance->setWebsiteId($website->getId());
    $customerBalance->save();

    /** @var $rewardPoints Enterprise_Reward_Model_Reward */
    $rewardPoints = Mage::getModel('Enterprise_Reward_Model_Reward');
    $rewardPoints->setCustomerId($customer->getId());
    $rewardPointsBalance = 100 + $increment;
    $registerKey = 'reward_point_balance_' . $website->getCode();
    Mage::unregister($registerKey);
    Mage::register($registerKey, $rewardPointsBalance);
    $rewardPoints->setPointsBalance($rewardPointsBalance);
    $rewardPoints->setWebsiteId($website->getId());
    $rewardPoints->save();
}
