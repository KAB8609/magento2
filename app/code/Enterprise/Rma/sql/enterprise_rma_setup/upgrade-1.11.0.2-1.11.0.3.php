<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_Rma_Model_Resource_Setup */
$installer = $this;

$tableName = $installer->getTable('sales_flat_order_item');

$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $tableName,
        'qty_returned',
        array(
            'TYPE'      => Magento_DB_Ddl_Table::TYPE_DECIMAL,
            'SCALE'     => 4,
            'PRECISION' => 12,
            'DEFAULT'   => '0.0000',
            'NULLABLE'  => false,
            'COMMENT'   => 'Qty of returned items',
        )
    );
