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

/** @var $quote Magento_Sales_Model_Quote */
$quote = Mage::getModel('Magento_Sales_Model_Quote');
$quote->setStoreId(1)
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->addProduct($product->load($product->getId()), 2);

$quote->getPayment()->setMethod('checkmo');

$quote->collectTotals();
$quote->save();

$quoteService = new Magento_Sales_Model_Service_Quote($quote);
$quoteService->getQuote()->getPayment()->setMethod('checkmo');