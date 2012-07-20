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

/**
 * Prepare database before module installation
 */
$installer->startSetup();

/**
 * Create table 'enterprise_rma'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'RMA Id')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'BugsCoverage')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Active')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Increment Id')
    ->addColumn('date_requested', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'RMA Requested At')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Order Id')
    ->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Order Increment Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Customer Id')
    ->addColumn('customer_custom_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Customer Custom Email')
    ->addIndex($installer->getIdxName('enterprise_rma', array('status')),
        array('status'))
    ->addIndex($installer->getIdxName('enterprise_rma', array('is_active')),
        array('is_active'))
    ->addIndex($installer->getIdxName('enterprise_rma', array('increment_id')),
        array('increment_id'))
    ->addIndex($installer->getIdxName('enterprise_rma', array('date_requested')),
        array('date_requested'))
    ->addIndex($installer->getIdxName('enterprise_rma', array('order_id')),
        array('order_id'))
    ->addIndex($installer->getIdxName('enterprise_rma', array('order_increment_id')),
        array('order_increment_id'))
    ->addIndex($installer->getIdxName('enterprise_rma', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('enterprise_rma', array('customer_id')),
        array('customer_id'))
    ->addForeignKey($installer->getFkName('enterprise_rma', 'customer_id', 'customer_entity', 'entity_id'),
        'customer_id', $installer->getTable('customer_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('enterprise_rma', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA LIst');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_rma_grid'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_grid'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'RMA Id')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'BugsCoverage')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Increment Id')
    ->addColumn('date_requested', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'RMA Requested At')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Order Id')
    ->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Order Increment Id')
    ->addColumn('order_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Order Created At')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Customer Id')
    ->addColumn('customer_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Customer Billing Name')
    ->addIndex($installer->getIdxName('enterprise_rma_grid', array('status')),
        array('status'))
    ->addIndex($installer->getIdxName('enterprise_rma_grid', array('increment_id')),
        array('increment_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_grid', array('date_requested')),
        array('date_requested'))
    ->addIndex($installer->getIdxName('enterprise_rma_grid', array('order_id')),
        array('order_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_grid', array('order_increment_id')),
        array('order_increment_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_grid', array('order_date')),
        array('order_date'))
    ->addIndex($installer->getIdxName('enterprise_rma_grid', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_grid', array('customer_id')),
        array('customer_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_grid', array('customer_name')),
        array('customer_name'))
    ->addForeignKey($installer->getFkName('enterprise_rma_grid', 'entity_id', 'enterprise_rma', 'entity_id'),
        'entity_id', $installer->getTable('enterprise_rma'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA Grid');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_rma_status_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_status_history'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('rma_entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'RMA Entity Id')
    ->addColumn('is_customer_notified', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Is Customer Notified')
    ->addColumn('is_visible_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Visible On Front')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Comment')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'BugsCoverage')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'Created At')
    ->addColumn('is_admin', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        ), 'Is this Merchant Comment')
    ->addIndex($installer->getIdxName('enterprise_rma_status_history', array('rma_entity_id')),
        array('rma_entity_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_status_history', array('created_at')),
        array('created_at'))
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_status_history', 'rma_entity_id', 'enterprise_rma', 'entity_id'),
        'rma_entity_id',
        $installer->getTable('enterprise_rma'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA status history enterprise_rma_status_history');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_rma_item_entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_item_entity'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Set Id')
    ->addColumn('rma_entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'RMA entity id')
    ->addColumn('is_qty_decimal', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Qty Decimal')
    ->addColumn('qty_requested', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Qty of requested for RMA items')
    ->addColumn('qty_authorized', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Qty of authorized items')
    ->addColumn('qty_approved', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Qty of approved items')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'BugsCoverage')
    ->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product Order Item Id')
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Product Name')
    ->addColumn('product_sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Product Sku')
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity', array('entity_type_id')),
        array('entity_type_id'))
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_entity', 'rma_entity_id', 'enterprise_rma', 'entity_id'),
        'rma_entity_id',
        $installer->getTable('enterprise_rma'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA Item Entity');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_rma_item_eav_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_item_eav_attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Id')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Visible')
    ->addColumn('input_filter', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Input Filter')
    ->addColumn('multiline_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Multiline Count')
    ->addColumn('validate_rules', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Validate Rules')
    ->addColumn('is_system', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is System')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addColumn('data_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Data Model')
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_eav_attribute', 'attribute_id', 'eav_attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA Item EAV Attribute');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_entity_datetime'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_item_entity_datetime'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'enterprise_rma_item_entity_datetime',
            array('entity_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('entity_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_datetime', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_datetime', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_datetime', array('entity_id')),
        array('entity_id'))
    ->addIndex(
        $installer->getIdxName('enterprise_rma_item_entity_datetime', array('entity_id', 'attribute_id', 'value')),
        array('entity_id', 'attribute_id', 'value'))
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_entity_datetime', 'attribute_id', 'eav_attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'enterprise_rma_item_entity_datetime',
            'entity_id',
            'enterprise_rma_item_entity',
            'entity_id'),
        'entity_id', $installer->getTable('enterprise_rma_item_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'enterprise_rma_item_entity_datetime',
            'entity_type_id',
            'eav_entity_type',
            'entity_type_id'),
        'entity_type_id', $installer->getTable('eav_entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA Item Entity Datetime');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_rma_item_entity_decimal'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_item_entity_decimal'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'enterprise_rma_item_entity_decimal',
            array('entity_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('entity_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_decimal', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_decimal', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_decimal', array('entity_id')),
        array('entity_id'))
    ->addIndex(
        $installer->getIdxName('enterprise_rma_item_entity_decimal', array('entity_id', 'attribute_id', 'value')),
        array('entity_id', 'attribute_id', 'value'))
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_entity_decimal', 'attribute_id', 'eav_attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'enterprise_rma_item_entity_decimal',
            'entity_id',
            'enterprise_rma_item_entity',
            'entity_id'),
        'entity_id', $installer->getTable('enterprise_rma_item_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'enterprise_rma_item_entity_decimal',
            'entity_type_id',
            'eav_entity_type',
            'entity_type_id'),
        'entity_type_id', $installer->getTable('eav_entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA Item Entity Decimal');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_rma_item_entity_int'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_item_entity_int'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'enterprise_rma_item_entity_int',
            array('entity_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('entity_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_int', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_int', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_int', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_int', array('entity_id', 'attribute_id', 'value')),
        array('entity_id', 'attribute_id', 'value'))
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_entity_int', 'attribute_id', 'eav_attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_entity_int', 'entity_id', 'enterprise_rma_item_entity', 'entity_id'),
        'entity_id', $installer->getTable('enterprise_rma_item_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_entity_int', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav_entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA Item Entity Int');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_rma_item_entity_text'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_item_entity_text'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => false,
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'enterprise_rma_item_entity_text',
            array('entity_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('entity_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_text', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_text', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_text', array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_entity_text', 'attribute_id', 'eav_attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'enterprise_rma_item_entity_text',
            'entity_id',
            'enterprise_rma_item_entity',
            'entity_id'),
        'entity_id', $installer->getTable('enterprise_rma_item_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_entity_text', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav_entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA Item Entity Text');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_rma_item_entity_varchar'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_item_entity_varchar'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'enterprise_rma_item_entity_varchar',
            array('entity_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('entity_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_varchar', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_varchar', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('enterprise_rma_item_entity_varchar', array('entity_id')),
        array('entity_id'))
    ->addIndex(
        $installer->getIdxName('enterprise_rma_item_entity_varchar', array('entity_id', 'attribute_id', 'value')),
        array('entity_id', 'attribute_id', 'value'))
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_entity_varchar', 'attribute_id', 'eav_attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'enterprise_rma_item_entity_varchar',
            'entity_id',
            'enterprise_rma_item_entity',
            'entity_id'),
        'entity_id', $installer->getTable('enterprise_rma_item_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'enterprise_rma_item_entity_varchar',
            'entity_type_id',
            'eav_entity_type',
            'entity_type_id'),
        'entity_type_id', $installer->getTable('eav_entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA Item Entity Varchar');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_rma_item_form_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_item_form_attribute'))
    ->addColumn('form_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Form Code')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Id')
    ->addIndex($installer->getIdxName('enterprise_rma_item_form_attribute', array('attribute_id')),
        array('attribute_id'))
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_form_attribute', 'attribute_id', 'eav_attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav_attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('RMA Item Form Attribute');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_rma_item_eav_attribute_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_rma_item_eav_attribute_website'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Is Visible')
    ->addColumn('is_required', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Is Required')
    ->addColumn('default_value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Default Value')
    ->addColumn('multiline_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Multiline Count')
    ->addIndex($installer->getIdxName('enterprise_rma_item_eav_attribute_website', array('website_id')),
        array('website_id'))
    ->addForeignKey(
        $installer->getFkName(
            'enterprise_rma_item_eav_attribute_website',
            'attribute_id',
            'eav_attribute',
            'attribute_id'),
        'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('enterprise_rma_item_eav_attribute_website', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise RMA Item Eav Attribute Website');
$installer->getConnection()->createTable($table);

$installer->endSetup();

$installer->installEntities();
$installer->installForms();

//Add Product's Attribute
$installer = Mage::getResourceModel('Mage_Catalog_Model_Resource_Setup', 'catalog_setup');

/**
 * Prepare database before module installation
 */
$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_returnable', array(
    'group'             => 'General',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Enable RMA',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'Mage_Eav_Model_Entity_Attribute_Source_Boolean',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '1',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          =>
        Mage_Catalog_Model_Product_Type::TYPE_SIMPLE . ',' .
        Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE . ',' .
        Mage_Catalog_Model_Product_Type::TYPE_GROUPED . ',' .
        Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
    'is_configurable'   => false,
    'input_renderer'    => 'Enterprise_Rma_Block_Adminhtml_Product_Renderer',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_config_is_returnable', array(
    'group'             => 'General',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Use Config Enable RMA',
    'input'             => 'text',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'           => false,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '1',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          =>
        Mage_Catalog_Model_Product_Type::TYPE_SIMPLE . ',' .
        Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE . ',' .
        Mage_Catalog_Model_Product_Type::TYPE_GROUPED . ',' .
        Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
    'is_configurable'   => false
));

$installer->endSetup();
