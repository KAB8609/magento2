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

require __DIR__ . '/../../../Magento/Catalog/_files/products.php';

/** @var $quote \Magento\Sales\Model\Quote */
$quote = Mage::getModel('\Magento\Sales\Model\Quote');
$quote->setStoreId(1)
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->addProduct($product->load($product->getId()), 2);

$quote->getPayment()->setMethod('checkmo');

$quote->collectTotals();
$quote->save();

$quoteService = new \Magento\Sales\Model\Service\Quote($quote);
$quoteService->getQuote()->getPayment()->setMethod('checkmo');
