<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
$defaultWebsiteId = Mage::app()->getStore()->getWebsiteId();

/** @var $website \Magento\Core\Model\Website */
$website = Mage::getModel('\Magento\Core\Model\Website');
$website->setData(array(
    'code'             => 'base2',
    'name'             => 'Test Website',
    'default_group_id' => '1',
    'is_default'       => '0'
));
$website->save();
Mage::app()->reinitStores();

$additionalWebsiteId = $website->getId();

Mage::unregister('_fixture/Magento_ScheduledImportExport_Model_TestWebsite');
Mage::register('_fixture/Magento_ScheduledImportExport_Model_TestWebsite', $website);

$expectedBalances = array();
$expectedRewards = array();

//Create customer
/** @var $customer \Magento\Customer\Model\Customer */
$customer = Mage::getModel('\Magento\Customer\Model\Customer');
$customer->setWebsiteId(0)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('BetsyParker@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Betsy')
    ->setLastname('Parker')
    ->setGender(2);
$customer->isObjectNew(true);
$customer->save();

/** @var $customerBalance \Magento\CustomerBalance\Model\Balance */
$customerBalance = Mage::getModel('\Magento\CustomerBalance\Model\Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(50);
$customerBalance->setWebsiteId($additionalWebsiteId);
$customerBalance->save();

/** @var $rewardPoints \Magento\Reward\Model\Reward */
$rewardPoints = Mage::getModel('\Magento\Reward\Model\Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(50);
$rewardPoints->setWebsiteId($additionalWebsiteId);
$rewardPoints->save();

$expectedBalances[$customer->getId()][$additionalWebsiteId] = 0;
$expectedRewards[$customer->getId()][$additionalWebsiteId] = 0;

/** @var $customer \Magento\Customer\Model\Customer */
$customer = Mage::getModel('\Magento\Customer\Model\Customer');
$customer->setWebsiteId(0)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('AnthonyNealy@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Anthony')
    ->setLastname('Nealy')
    ->setGender(1);
$customer->isObjectNew(true);
$customer->save();

/** @var $customerBalance \Magento\CustomerBalance\Model\Balance */
$customerBalance = Mage::getModel('\Magento\CustomerBalance\Model\Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(100);
$customerBalance->setWebsiteId($defaultWebsiteId);
$customerBalance->save();

/** @var $rewardPoints \Magento\Reward\Model\Reward */
$rewardPoints = Mage::getModel('\Magento\Reward\Model\Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(100);
$rewardPoints->setWebsiteId($defaultWebsiteId);
$rewardPoints->save();

$expectedBalances[$customer->getId()][$defaultWebsiteId] = 0;
$expectedRewards[$customer->getId()][$defaultWebsiteId] = 0;

/** @var $customer \Magento\Customer\Model\Customer */
$customer = Mage::getModel('\Magento\Customer\Model\Customer');
$customer->setWebsiteId(0)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('LoriBanks@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Lori')
    ->setLastname('Banks')
    ->setGender(2);
$customer->isObjectNew(true);
$customer->save();

/** @var $customerBalance \Magento\CustomerBalance\Model\Balance */
$customerBalance = Mage::getModel('\Magento\CustomerBalance\Model\Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(200);
$customerBalance->setWebsiteId($additionalWebsiteId);
$customerBalance->save();

/** @var $rewardPoints \Magento\Reward\Model\Reward */
$rewardPoints = Mage::getModel('\Magento\Reward\Model\Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(200);
$rewardPoints->setWebsiteId($additionalWebsiteId);
$rewardPoints->save();

$expectedBalances[$customer->getId()][$additionalWebsiteId] = 200;
$expectedRewards[$customer->getId()][$additionalWebsiteId] = 200;

/** @var $customerBalance \Magento\CustomerBalance\Model\Balance */
$customerBalance = Mage::getModel('\Magento\CustomerBalance\Model\Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(300);
$customerBalance->setWebsiteId($defaultWebsiteId);
$customerBalance->save();

/** @var $rewardPoints \Magento\Reward\Model\Reward */
$rewardPoints = Mage::getModel('\Magento\Reward\Model\Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(300);
$rewardPoints->setWebsiteId($defaultWebsiteId);
$rewardPoints->save();

$expectedBalances[$customer->getId()][$defaultWebsiteId] = 300;
$expectedRewards[$customer->getId()][$defaultWebsiteId] = 300;

$customer = Mage::getModel('\Magento\Customer\Model\Customer');
$customer->setWebsiteId(0)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('PatriciaPPerez@magento.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Patricia')
    ->setLastname('Perez')
    ->setGender(2);
$customer->isObjectNew(true);
$customer->save();

/** @var $customerBalance \Magento\CustomerBalance\Model\Balance */
$customerBalance = Mage::getModel('\Magento\CustomerBalance\Model\Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(400);
$customerBalance->setWebsiteId($additionalWebsiteId);
$customerBalance->save();

/** @var $rewardPoints \Magento\Reward\Model\Reward */
$rewardPoints = Mage::getModel('\Magento\Reward\Model\Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(400);
$rewardPoints->setWebsiteId($additionalWebsiteId);
$rewardPoints->save();

$expectedBalances[$customer->getId()][$additionalWebsiteId] = 0;
$expectedRewards[$customer->getId()][$additionalWebsiteId] = 0;

/** @var $customerBalance \Magento\CustomerBalance\Model\Balance */
$customerBalance = Mage::getModel('\Magento\CustomerBalance\Model\Balance');
$customerBalance->setCustomerId($customer->getId());
$customerBalance->setAmountDelta(500);
$customerBalance->setWebsiteId($defaultWebsiteId);
$customerBalance->save();

/** @var $rewardPoints \Magento\Reward\Model\Reward */
$rewardPoints = Mage::getModel('\Magento\Reward\Model\Reward');
$rewardPoints->setCustomerId($customer->getId());
$rewardPoints->setPointsBalance(500);
$rewardPoints->setWebsiteId($defaultWebsiteId);
$rewardPoints->save();

$expectedBalances[$customer->getId()][$defaultWebsiteId] = 500;
$expectedRewards[$customer->getId()][$defaultWebsiteId] = 500;

Mage::unregister('_fixture/Magento_ScheduledImportExport_Customers_ExpectedBalances');
Mage::register('_fixture/Magento_ScheduledImportExport_Customers_ExpectedBalances', $expectedBalances);

Mage::unregister('_fixture/Magento_ScheduledImportExport_Customers_ExpectedRewards');
Mage::register('_fixture/Magento_ScheduledImportExport_Customers_ExpectedRewards', $expectedRewards);
