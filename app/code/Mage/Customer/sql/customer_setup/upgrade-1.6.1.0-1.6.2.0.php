<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;

$disableAGCAttributeCode = 'disable_auto_group_change';

$installer->addAttribute('customer', $disableAGCAttributeCode, array(
    'type'      => 'static',
    'label'     => 'Disable Automatic Group Change Based on VAT ID',
    'input'     => 'boolean',
    'backend'   => 'Mage_Customer_Model_Attribute_Backend_Data_Boolean',
    'position'  => 28,
    'required'  => false
));

$attributesInfo = array(
    'vat_id' => array(
        'label'     => 'VAT number',
        'type'      => 'varchar',
        'input'     => 'text',
        'position'  => 140,
        'visible'   => true,
        'required'  => false
    ),
    'vat_is_valid' => array(
        'label'     => 'VAT number validity',
        'visible'   => false,
        'required'  => false,
        'type'      => 'int'
    ),
    'vat_request_id' => array(
        'label'     => 'VAT number validation request ID',
        'type'      => 'varchar',
        'visible'   => false,
        'required'  => false
    ),
    'vat_request_date' => array(
        'label'     => 'VAT number validation request date',
        'type'      => 'varchar',
        'visible'   => false,
        'required'  => false
    ),
    'vat_request_success' => array(
        'label'     => 'VAT number validation request success',
        'visible'   => false,
        'required'  => false,
        'type'      => 'int'
    )
);

foreach ($attributesInfo as $attributeCode => $attributeParams) {
    $installer->addAttribute('customer_address', $attributeCode, $attributeParams);
}
