<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('COUNT_CUSTOMER_ADDRESES', 3);

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixtures');

/* @var $customerFixture Mage_Customer_Model_Customer */
$customerFixture = require $fixturesDir . '/Customer/Customer.php';

/* @var $customerAddressFixture Mage_Customer_Model_Address */
$customerAddressFixture = require $fixturesDir . '/Customer/Address.php';

// Create customer
$customerFixture->save();

// Get address eav required attributes
$requiredAttributes = array();
foreach (Mage::getModel('customer/address')->getAttributes() as $attribute) {
    if ($attribute->getIsRequired() && $attribute->getIsVisible()) {
        $requiredAttributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
    }
}

// Create addresses
for ($i = 0; $i < COUNT_CUSTOMER_ADDRESES; $i++) {
    $address = clone $customerAddressFixture;
    $address->setCustomer($customerFixture);
    foreach ($requiredAttributes as $requiredAttributes => $requiredAttribute) {
        if (!in_array($requiredAttributes, array('country_id', 'region'))) {
            $requiredAttribute .= uniqid();
        }
        $address->setData($attributeCode, $requiredAttribute);
    }
    $address->save();
}

Magento_Test_Webservice::setFixture('customer',
    Mage::getModel('customer/customer')->load($customerFixture->getId())); // for load addresses collection
