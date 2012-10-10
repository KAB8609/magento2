<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftCard
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$billingAddress = Mage::getModel('Mage_Sales_Model_Order_Address',
    array(
        'data' => array(
            'firstname'  => 'guest',
            'lastname'   => 'guest',
            'email'      => 'customer@example.com',
            'street'     => 'street',
            'city'       => 'Los Angeles',
            'region'     => 'CA',
            'postcode'   => '1',
            'country_id' => 'US',
            'telephone'  => '1',
        )
    )
);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

$payment = Mage::getModel('Mage_Sales_Model_Order_Payment');
$payment->setMethod('checkmo');

$orderItem = Mage::getModel('Mage_Sales_Model_Order_Item');
$orderItem->setProductId(1)
    ->setProductType(Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD)
    ->setBasePrice(100)
    ->setQtyOrdered(1)
    ->setProductOptions(array(
        'giftcard_amount'         => 'custom',
        'custom_giftcard_amount'  => 100,
        'giftcard_sender_name'    => 'Gift Card Sender Name',
        'giftcard_sender_email'   => 'sender@example.com',
        'giftcard_recipient_name' => 'Gift Card Recipient Name',
        'giftcard_recipient_email'=> 'recipient@example.com',
        'giftcard_message'        => 'Gift Card Message',
        'giftcard_email_template' => 'giftcard_email_template',
    ));

$order = Mage::getModel('Mage_Sales_Model_Order');
$order->addItem($orderItem)
    ->setIncrementId('100000001')
    ->setCustomerIsGuest(true)
    ->setStoreId(1)
    ->setEmailSent(1)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setPayment($payment);
$order->save();

Mage::getConfig()->setNode('websites/base/giftcard/giftcardaccount_general/pool_size', 1);
$pool = Mage::getModel('Enterprise_GiftCardAccount_Model_Pool');
$pool->setWebsiteId(1)->generatePool();
