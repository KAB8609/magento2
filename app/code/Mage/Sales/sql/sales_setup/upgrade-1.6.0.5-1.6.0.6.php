<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;

$entitiesToAlter = array(
    'quote_address',
    'order_address'
);

$attributes = array(
    'vat_id' => array('type' => Magento_DB_Ddl_Table::TYPE_TEXT),
    'vat_is_valid' => array('type' => Magento_DB_Ddl_Table::TYPE_SMALLINT),
    'vat_request_id' => array('type' => Magento_DB_Ddl_Table::TYPE_TEXT),
    'vat_request_date' => array('type' => Magento_DB_Ddl_Table::TYPE_TEXT),
    'vat_request_success' => array('type' => Magento_DB_Ddl_Table::TYPE_SMALLINT)
);

foreach ($entitiesToAlter as $entityName) {
    foreach ($attributes as $attributeCode => $attributeParams) {
        $installer->addAttribute($entityName, $attributeCode, $attributeParams);
    }
}
