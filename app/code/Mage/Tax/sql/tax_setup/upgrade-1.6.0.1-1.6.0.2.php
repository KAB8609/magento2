<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Tax_Model_Resource_Setup */
$installer = $this;

/**
 * Create table 'sales_order_tax_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sales_order_tax_item'))
    ->addColumn('tax_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Tax Item Id')
    ->addColumn('tax_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Tax Id')
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Item Id')
    ->addIndex($installer->getIdxName('sales_order_tax_item', array('tax_id')),
        array('tax_id'))
    ->addIndex($installer->getIdxName('sales_order_tax_item', array('item_id')),
        array('item_id'))
    ->addIndex(
        $installer->getIdxName(
            'sales_order_tax_item', array('tax_id', 'item_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('tax_id', 'item_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales_order_tax_item',
            'tax_id',
            'sales_order_tax',
            'tax_id'
        ),
        'tax_id',
        $installer->getTable('sales_order_tax'),
        'tax_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'sales_order_tax_item',
            'item_id',
            'sales_flat_order_item',
            'item_id'
        ),
        'item_id',
        $installer->getTable('sales_flat_order_item'),
        'item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Sales Order Tax Item');
$installer->getConnection()->createTable($table);
