<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$payment = Mage::getModel('Magento_Sales_Model_Order_Payment');
$payment->setMethod('checkmo');

$order = Mage::getModel('Magento_Sales_Model_Order');
$order->setIncrementId('100000001')
    ->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setCustomerIsGuest(true)
    ->setPayment($payment);

$payment->setTransactionId('trx1');
$payment->addTransaction(Magento_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);

$order->save();