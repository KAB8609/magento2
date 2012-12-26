<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $productFixture Mage_Catalog_Model_Product */
$productFixture = require '_fixture/_block/Catalog/Product.php';

/* @var $quote Mage_Sales_Model_Quote */
$quote = require '_fixture/_block/Sales/Quote/Quote.php';

/* @var $address Mage_Sales_Model_Quote_Address */
$address = require '_fixture/_block/Sales/Quote/Address.php';

/* @var $rateFixture Mage_Sales_Model_Quote_Address_Rate */
$rateFixture = require '_fixture/_block/Sales/Quote/Rate.php';

/* @var $customer Mage_Customer_Model_Customer */
$customer = Mage::getModel('Mage_Customer_Model_Customer');
$customer->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);


// Create products
$product1 = clone $productFixture;
$product1->save();
$product2 = clone $productFixture;
$product2->save();

// Create quote
$quote->assignCustomer($customer);
$quote->addProduct($product1, 1);
$quote->addProduct($product2, 2);

$shippingAddress = clone $address;
$quote->setShippingAddress(clone $address);

$billingAddress = clone $address;
$quote->setBillingAddress(clone $address);

$quote->getShippingAddress()->addShippingRate($rateFixture);
$quote->collectTotals()
    ->save();

//Create order
$quoteService = new Mage_Sales_Model_Service_Quote($quote);
$order = $quoteService->submitOrder()
    ->place()
    ->save();

Magento_Test_TestCase_ApiAbstract::setFixture('customer_product1', $product1);
Magento_Test_TestCase_ApiAbstract::setFixture('customer_product2', $product2);
Magento_Test_TestCase_ApiAbstract::setFixture('customer_quote', $quote);
Magento_Test_TestCase_ApiAbstract::setFixture(
    'customer_order',
    Mage::getModel('Mage_Sales_Model_Order')->load($order->getId())
);
