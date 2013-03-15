<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$ruleProductTable = $installer->getTable('catalogrule_product');

$columnOptions = array(
    'TYPE' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'LENGTH' => 32,
    'COMMENT' => 'Simple Action For Subitems',
);
$installer->getConnection()->addColumn($ruleProductTable, 'sub_simple_action', $columnOptions);

$columnOptions = array(
    'TYPE' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'SCALE' => 4,
    'PRECISION' => 12,
    'NULLABLE' => false,
    'DEFAULT' => '0.0000',
    'COMMENT' => 'Discount Amount For Subitems',
);
$installer->getConnection()->addColumn($ruleProductTable, 'sub_discount_amount', $columnOptions);

$installer->endSetup();
