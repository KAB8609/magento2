<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('COUNT_CUSTOMER_ADDRESES', 3);

/* @var $customerFixture Mage_Customer_Model_Customer */
$customerFixture = require '_fixture/_block/Customer/Customer.php';

/* @var $customerAddressFixture Mage_Customer_Model_Address */
$customerAddressFixture = require '_fixture/_block/Customer/Address.php';

// Create customer
$customerFixture->save();

// Get address eav required attributes
$requiredAttributes = array();
foreach (Mage::getModel('Mage_Customer_Model_Address')->getAttributes() as $attribute) {
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

Magento_Test_TestCase_ApiAbstract::setFixture(
    'customer',
    Mage::getModel('Mage_Customer_Model_Customer')->load($customerFixture->getId())
); // for load addresses collection
