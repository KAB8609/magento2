<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

Mage::app()->loadArea('frontend');
include 'order_with_shipping.php';
/** @var Mage_Sales_Model_Order $order */

$shipment = $order->prepareShipment();
$shipment->register();
$shipment->getOrder()->setIsInProcess(true);
/** @var Magento_Core_Model_Resource_Transaction $transaction */
$transaction = Mage::getModel('Magento_Core_Model_Resource_Transaction');
$transaction->addObject($shipment)->addObject($order)->save();

Mage::register('shipment', $shipment);
