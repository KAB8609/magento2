<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!isset($customersNumber)) {
    $customersNumber = 1;
}

$pattern = array(
    'email'                       => 'user@example.com',
    '_website'                    => 'base',
    '_store'                      => '',
    'confirmation'                => NULL,
    'created_at'                  => '30-08-2012 17:43',
    'created_in'                  => 'Default',
    'default_billing'             => '1',
    'default_shipping'            => '1',
    'disable_auto_group_change'   => '0',
    'dob'                         => '12-10-1991',
    'firstname'                   => 'Firstname',
    'gender'                      => 'Male',
    'group_id'                    => '1',
    'lastname'                    => 'Lastname',
    'middlename'                  => '',
    'password_hash'               => '',
    'prefix'                      => NULL,
    'rp_token'                    => NULL,
    'rp_token_created_at'         => NULL,
    'store_id'                    => '0',
    'suffix'                      => NULL,
    'taxvat'                      => NULL,
    'website_id'                  => '1',
    'password'                    => '123123q',
    '_address_city'               => 'Fayetteville',
    '_address_company'            => '',
    '_address_country_id'         => 'US',
    '_address_fax'                => '',
    '_address_firstname'          => 'Anthony',
    '_address_lastname'           => 'Nealy',
    '_address_middlename'         => '',
    '_address_postcode'           => '123123',
    '_address_prefix'             => '',
    '_address_region'             => 'Arkansas',
    '_address_street'             => '123 Freedom Blvd. #123',
    '_address_suffix'             => '',
    '_address_telephone'          => '022-333-4455',
    '_address_vat_id'             => '',
    '_address_default_billing_'   => '1',
    '_address_default_shipping_'  => '1'
);
$generator = new Magento_TestFramework_ImportExport_Fixture_Generator($pattern, $customersNumber);
/** @var Magento_ImportExport_Model_Import $import */
$import = Mage::getModel(
    'Magento_ImportExport_Model_Import',
    array('data' => array('entity' => 'customer_composite', 'behavior' => 'append'))
);
// it is not obvious, but the validateSource() will actually save import queue data to DB
$import->validateSource($generator);
// this converts import queue into actual entities
$import->importSource();
