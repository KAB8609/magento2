<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $rate Mage_Sales_Model_Quote_Address_Rate */
$rate = Mage::getModel('sales/quote_address_rate');
$rate->setCode('freeshipping_freeshipping')
    ->getPrice(1);
return $rate;