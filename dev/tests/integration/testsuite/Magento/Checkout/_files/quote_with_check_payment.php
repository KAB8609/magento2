<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'quote_with_address.php';
/** @var \Magento\Sales\Model\Quote $quote */

/** @var $rate \Magento\Sales\Model\Quote\Address\Rate */
$rate = Mage::getModel('Magento\Sales\Model\Quote\Address\Rate');
$rate->setCode('freeshipping_freeshipping');
$rate->getPrice(1);

$quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
$quote->getShippingAddress()->addShippingRate($rate);
$quote->getPayment()->setMethod('checkmo');

$quote->collectTotals();
$quote->save();

$quoteService = new \Magento\Sales\Model\Service\Quote($quote);
$quoteService->getQuote()->getPayment()->setMethod('checkmo');
