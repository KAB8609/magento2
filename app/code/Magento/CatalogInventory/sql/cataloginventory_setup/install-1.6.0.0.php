<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Eav\Model\Entity\Setup */

$installer->startSetup();

/**
 * Create table 'cataloginventory_stock'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cataloginventory_stock'))
    ->addColumn('stock_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Stock Id')
    ->addColumn('stock_name', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Stock Name')
    ->setComment('Cataloginventory Stock');
$installer->getConnection()->createTable($table);

/**
 * Create table 'cataloginventory_stock_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cataloginventory_stock_item'))
    ->addColumn('item_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Item Id')
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product Id')
    ->addColumn('stock_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Stock Id')
    ->addColumn('qty', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Qty')
    ->addColumn('min_qty', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Min Qty')
    ->addColumn('use_config_min_qty', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Use Config Min Qty')
    ->addColumn('is_qty_decimal', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Qty Decimal')
    ->addColumn('backorders', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Backorders')
    ->addColumn('use_config_backorders', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Use Config Backorders')
    ->addColumn('min_sale_qty', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '1.0000',
        ), 'Min Sale Qty')
    ->addColumn('use_config_min_sale_qty', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Use Config Min Sale Qty')
    ->addColumn('max_sale_qty', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Max Sale Qty')
    ->addColumn('use_config_max_sale_qty', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Use Config Max Sale Qty')
    ->addColumn('is_in_stock', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is In Stock')
    ->addColumn('low_stock_date', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Low Stock Date')
    ->addColumn('notify_stock_qty', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        ), 'Notify Stock Qty')
    ->addColumn('use_config_notify_stock_qty', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Use Config Notify Stock Qty')
    ->addColumn('manage_stock', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Manage Stock')
    ->addColumn('use_config_manage_stock', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Use Config Manage Stock')
    ->addColumn('stock_status_changed_auto', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Stock Status Changed Automatically')
    ->addColumn('use_config_qty_increments', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Use Config Qty Increments')
    ->addColumn('qty_increments', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Qty Increments')
    ->addColumn('use_config_enable_qty_inc', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Use Config Enable Qty Increments')
    ->addColumn('enable_qty_increments', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Enable Qty Increments')
    ->addIndex($installer->getIdxName('cataloginventory_stock_item', array('product_id', 'stock_id'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('product_id', 'stock_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex($installer->getIdxName('cataloginventory_stock_item', array('product_id')),
        array('product_id')
    )
    ->addIndex($installer->getIdxName('cataloginventory_stock_item', array('stock_id')),
        array('stock_id')
    )
    ->addForeignKey($installer->getFkName('cataloginventory_stock_item', 'product_id', 'catalog_product_entity', 'entity_id'),
        'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'cataloginventory_stock_item', 'stock_id', 'cataloginventory_stock', 'stock_id'
        ),
        'stock_id', $installer->getTable('cataloginventory_stock'), 'stock_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Cataloginventory Stock Item');
$installer->getConnection()->createTable($table);

/**
 * Create table 'cataloginventory_stock_status'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cataloginventory_stock_status'))
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Product Id')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addColumn('stock_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Stock Id')
    ->addColumn('qty', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Qty')
    ->addColumn('stock_status', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Stock Status')
    ->addIndex($installer->getIdxName('cataloginventory_stock_status', array('stock_id')),
        array('stock_id')
    )
    ->addIndex($installer->getIdxName('cataloginventory_stock_status', array('website_id')),
        array('website_id')
    )
    ->addForeignKey(
        $installer->getFkName(
            'cataloginventory_stock_status', 'stock_id', 'cataloginventory_stock', 'stock_id'
        ),
        'stock_id', $installer->getTable('cataloginventory_stock'), 'stock_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'cataloginventory_stock_status', 'product_id', 'catalog_product_entity', 'entity_id'
        ),
        'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey($installer->getFkName('cataloginventory_stock_status', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Cataloginventory Stock Status');
$installer->getConnection()->createTable($table);

/**
 * Create table 'cataloginventory_stock_status_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cataloginventory_stock_status_idx'))
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Product Id')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addColumn('stock_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Stock Id')
    ->addColumn('qty', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Qty')
    ->addColumn('stock_status', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Stock Status')
    ->addIndex($installer->getIdxName('cataloginventory_stock_status_idx', array('stock_id')),
        array('stock_id')
    )
    ->addIndex($installer->getIdxName('cataloginventory_stock_status_idx', array('website_id')),
        array('website_id')
    )
    ->setComment('Cataloginventory Stock Status Indexer Idx');
$installer->getConnection()->createTable($table);

/**
 * Create table 'cataloginventory_stock_status_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cataloginventory_stock_status_tmp'))
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Product Id')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addColumn('stock_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Stock Id')
    ->addColumn('qty', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Qty')
    ->addColumn('stock_status', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Stock Status')
    ->addIndex($installer->getIdxName('cataloginventory_stock_status_tmp', array('stock_id')),
        array('stock_id')
    )
    ->addIndex($installer->getIdxName('cataloginventory_stock_status_tmp', array('website_id')),
        array('website_id')
    )
    ->setComment('Cataloginventory Stock Status Indexer Tmp');
$installer->getConnection()->createTable($table);

$installer->endSetup();

$installer->getConnection()->insertForce($installer->getTable('cataloginventory_stock'), array(
    'stock_id'      => 1,
    'stock_name'    => 'Default'
));

