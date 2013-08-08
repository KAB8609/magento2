<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_CatalogEvent_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'enterprise_catalogevent_event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_catalogevent_event'))
    ->addColumn('event_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Event Id')
    ->addColumn('category_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Category Id')
    ->addColumn('date_start', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Date Start')
    ->addColumn('date_end', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Date End')
    ->addColumn('display_state', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Display State')
    ->addColumn('sort_order', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Sort Order')
    ->addIndex($installer->getIdxName('enterprise_catalogevent_event', array('category_id'), true),
        array('category_id'), array('type' => 'unique'))
    ->addIndex($installer->getIdxName('enterprise_catalogevent_event', array('date_start', 'date_end')),
        array('date_start', 'date_end'))
    ->addForeignKey($installer->getFkName('enterprise_catalogevent_event', 'category_id', 'catalog_category_entity', 'entity_id'),
        'category_id', $installer->getTable('catalog_category_entity'), 'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Catalogevent Event');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_catalogevent_event_image'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_catalogevent_event_image'))
    ->addColumn('event_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Event Id')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store Id')
    ->addColumn('image', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Image')
    ->addIndex($installer->getIdxName('enterprise_catalogevent_event_image', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('enterprise_catalogevent_event_image', 'event_id', 'enterprise_catalogevent_event', 'event_id'),
        'event_id', $installer->getTable('enterprise_catalogevent_event'), 'event_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('enterprise_catalogevent_event_image', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Catalogevent Event Image');
$installer->getConnection()->createTable($table);

$installer->addAttribute('quote_item', 'event_id', array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER));
$installer->addAttribute('order_item', 'event_id', array('type' => Magento_DB_Ddl_Table::TYPE_INTEGER));

$installer->endSetup();
