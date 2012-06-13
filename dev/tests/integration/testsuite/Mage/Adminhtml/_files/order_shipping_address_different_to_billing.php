<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
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

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setPostcode('2')
    ->setAddressType('shipping');

$order = new Mage_Sales_Model_Order();
$order->loadByIncrementId('100000001');
$clonedOrder = clone $order;
$order->setIncrementId('100000002');
$order->save();

$payment = new Mage_Sales_Model_Order_Payment();
$payment->setMethod('checkmo');

$order = $clonedOrder;
$order->setId(null)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setPayment($payment);
$order->save();
