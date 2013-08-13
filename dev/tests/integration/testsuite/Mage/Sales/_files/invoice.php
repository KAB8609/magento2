<?php
/**
 * Paid invoice fixture.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

require 'order.php';
/** @var Mage_Sales_Model_Order $order */

$orderService = new Mage_Sales_Model_Service_Order($order);
$invoice = $orderService->prepareInvoice();
$invoice->register();
$order->setIsInProcess(true);
$transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction');
$transactionSave->addObject($invoice)->addObject($order)->save();
