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

$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getPayment()->setMethod('ccsave');

$quote->collectTotals();
$quote->save();

$quoteService = new \Magento\Sales\Model\Service\Quote($quote);
$quoteService->getQuote()->getPayment()->setMethod('ccsave');
