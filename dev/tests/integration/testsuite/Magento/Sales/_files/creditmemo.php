<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/order.php';

/** @var Magento_Sales_Model_Order $order */
$order = Mage::getModel('Magento_Sales_Model_Order');
$order->loadByIncrementId('100000001');

$order->setData('base_to_global_rate', 2)
    ->setData('base_total_refunded', 50)
    ->setData('base_total_online_refunded', 40)
    ->setData('base_total_offline_refunded', 10)
    ->setData('total_refunded', 100)
    ->save();