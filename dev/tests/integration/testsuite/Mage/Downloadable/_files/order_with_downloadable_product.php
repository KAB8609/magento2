<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Downlodable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$billingAddress = new Mage_Sales_Model_Order_Address(array(
    'firstname'  => 'guest',
    'lastname'   => 'guest',
    'email'      => 'customer@example.com',
    'street'     => 'street',
    'city'       => 'Los Angeles',
    'region'     => 'CA',
    'postcode'   => '1',
    'country_id' => 'US',
    'telephone'  => '1',
));
$billingAddress->setAddressType('billing');

$payment = new Mage_Sales_Model_Order_Payment();
$payment->setMethod('checkmo');

$orderItem = new Mage_Sales_Model_Order_Item();
$orderItem->setProductId(1)
    ->setProductType(Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE)
    ->setBasePrice(100)
    ->setQtyOrdered(1);

$order = new Mage_Sales_Model_Order();
$order->addItem($orderItem)
    ->setIncrementId('100000001')
    ->setCustomerIsGuest(true)
    ->setStoreId(1)
    ->setEmailSent(1)
    ->setBillingAddress($billingAddress)
    ->setPayment($payment);
$order->save();
