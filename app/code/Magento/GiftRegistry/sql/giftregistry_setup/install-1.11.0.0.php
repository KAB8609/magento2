<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_GiftRegistry_Model_Resource_Setup */
$installer = $this;

/**
 * Prepare database before module installation
 */
$installer->startSetup();

/**
 * Create table 'magento_giftregistry_type'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_giftregistry_type'))
    ->addColumn('type_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Type Id')
    ->addColumn('code', \Magento\DB\Ddl\Table::TYPE_TEXT, 15, array(
        'nullable'  => true,
        ), 'Code')
    ->addColumn('meta_xml', \Magento\DB\Ddl\Table::TYPE_BLOB, '64K', array(
        ), 'Meta Xml')
    ->setComment('Enterprise Gift Registry Type Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_giftregistry_type_info'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_giftregistry_type_info'))
    ->addColumn('type_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Type Id')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('label', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Label')
    ->addColumn('is_listed', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Is Listed')
    ->addColumn('sort_order', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Sort Order')
    ->addIndex($installer->getIdxName('magento_giftregistry_type_info', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(
            'magento_giftregistry_type_info',
            'type_id',
            'magento_giftregistry_type',
            'type_id'
        ),
        'type_id', $installer->getTable('magento_giftregistry_type'), 'type_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magento_giftregistry_type_info', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Gift Registry Info Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_giftregistry_label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_giftregistry_label'))
    ->addColumn('type_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Type Id')
    ->addColumn('attribute_code', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array(
        'primary'   => true,
        'nullable'  => false,
        ), 'Attribute Code')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('option_code', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array(
        'primary'   => true,
        'nullable'  => false,
        ), 'Option Code')
    ->addColumn('label', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Label')
    ->addIndex($installer->getIdxName('magento_giftregistry_label', array('type_id')),
        array('type_id'))
    ->addIndex($installer->getIdxName('magento_giftregistry_label', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(
            'magento_giftregistry_label',
            'type_id',
            'magento_giftregistry_type',
            'type_id'
        ),
        'type_id', $installer->getTable('magento_giftregistry_type'), 'type_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magento_giftregistry_label', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Gift Registry Label Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_giftregistry_entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_giftregistry_entity'))
    ->addColumn('entity_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('type_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Type Id')
    ->addColumn('customer_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Id')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('is_public', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Public')
    ->addColumn('url_key', \Magento\DB\Ddl\Table::TYPE_TEXT, 100, array(
        ), 'Url Key')
    ->addColumn('title', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Title')
    ->addColumn('message', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        'nullable'  => false,
        ), 'Message')
    ->addColumn('shipping_address', \Magento\DB\Ddl\Table::TYPE_BLOB, '64K', array(
        ), 'Shipping Address')
    ->addColumn('custom_values', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Custom Values')
    ->addColumn('is_active', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Active')
    ->addColumn('created_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Created At')
    ->addIndex($installer->getIdxName('magento_giftregistry_entity', array('customer_id')),
        array('customer_id'))
    ->addIndex($installer->getIdxName('magento_giftregistry_entity', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('magento_giftregistry_entity', array('type_id')),
        array('type_id'))
    ->addForeignKey(
        $installer->getFkName(
            'magento_giftregistry_entity',
            'type_id',
            'magento_giftregistry_type',
            'type_id'
        ),
        'type_id', $installer->getTable('magento_giftregistry_type'), 'type_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'magento_giftregistry_entity',
            'customer_id',
            'customer_entity',
            'entity_id'
        ),
        'customer_id', $installer->getTable('customer_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'magento_giftregistry_entity',
            'website_id',
            'core_website',
            'website_id'
        ),
        'website_id', $installer->getTable('core_website'), 'website_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Gift Registry Entity Table');

$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_giftregistry_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_giftregistry_item'))
    ->addColumn('item_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Item Id')
    ->addColumn('entity_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product Id')
    ->addColumn('qty', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        ), 'Qty')
    ->addColumn('qty_fulfilled', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        ), 'Qty Fulfilled')
    ->addColumn('note', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Note')
    ->addColumn('added_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Added At')
    ->addColumn('custom_options', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Custom Options')
    ->addIndex($installer->getIdxName('magento_giftregistry_item', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('magento_giftregistry_item', array('product_id')),
        array('product_id'))
    ->addForeignKey(
        $installer->getFkName(
            'magento_giftregistry_item',
            'entity_id',
            'magento_giftregistry_entity',
            'entity_id'
        ),
        'entity_id', $installer->getTable('magento_giftregistry_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'magento_giftregistry_item',
            'product_id',
            'catalog_product_entity',
            'entity_id'
        ),
        'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Gift Registry Item Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_giftregistry_person'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_giftregistry_person'))
    ->addColumn('person_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Person Id')
    ->addColumn('entity_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('firstname', \Magento\DB\Ddl\Table::TYPE_TEXT, 100, array(
        'nullable'  => true,
        ), 'Firstname')
    ->addColumn('lastname', \Magento\DB\Ddl\Table::TYPE_TEXT, 100, array(
        'nullable'  => true,
        ), 'Lastname')
    ->addColumn('email', \Magento\DB\Ddl\Table::TYPE_TEXT, 150, array(
        'nullable'  => true,
        ), 'Email')
    ->addColumn('role', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array(
        'nullable'  => true,
        ), 'Role')
    ->addColumn('custom_values', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        'nullable'  => false,
        ), 'Custom Values')
    ->addIndex($installer->getIdxName('magento_giftregistry_person', array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName(
            'magento_giftregistry_person',
            'entity_id',
            'magento_giftregistry_entity',
            'entity_id'
        ),
        'entity_id', $installer->getTable('magento_giftregistry_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Gift Registry Person Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_giftregistry_data'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_giftregistry_data'))
    ->addColumn('entity_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('event_date', \Magento\DB\Ddl\Table::TYPE_DATE, null, array(
        ), 'Event Date')
    ->addColumn('event_country', \Magento\DB\Ddl\Table::TYPE_TEXT, 3, array(
        ), 'Event Country')
    ->addColumn('event_country_region', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        ), 'Event Country Region')
    ->addColumn('event_country_region_text', \Magento\DB\Ddl\Table::TYPE_TEXT, 30, array(
        ), 'Event Country Region Text')
    ->addColumn('event_location', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Event Location')
    ->addForeignKey(
        $installer->getFkName(
            'magento_giftregistry_data',
            'entity_id',
            'magento_giftregistry_entity',
            'entity_id'
        ),
        'entity_id', $installer->getTable('magento_giftregistry_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Gift Registry Data Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_giftregistry_item_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_giftregistry_item_option'))
    ->addColumn('option_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Option Id')
    ->addColumn('item_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Item Id')
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product Id')
    ->addColumn('code', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Code')
    ->addColumn('value', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        'nullable'  => false,
        ), 'Value')
    ->addForeignKey(
        $installer->getFkName(
            'magento_giftregistry_item_option',
            'item_id',
            'magento_giftregistry_item',
            'item_id'
        ),
        'item_id', $installer->getTable('magento_giftregistry_item'), 'item_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Gift Registry Item Option Table');
$installer->getConnection()->createTable($table);

/**
 * Add attributes
 */
$installer->addAttribute(
    'quote_item', 'giftregistry_item_id', array('type' => \Magento\DB\Ddl\Table::TYPE_INTEGER, 'visible' => false)
);
$installer->addAttribute(
    'order_item', 'giftregistry_item_id', array('type' => \Magento\DB\Ddl\Table::TYPE_INTEGER, 'visible' => false)
);
$installer->addAttribute(
    'quote_address', 'giftregistry_item_id', array('type' => \Magento\DB\Ddl\Table::TYPE_INTEGER, 'visible' => false)
);
$installer->addAttribute(
    'order_address', 'giftregistry_item_id', array('type' => \Magento\DB\Ddl\Table::TYPE_INTEGER, 'visible' => false)
);

/**
 * Prepare database after module installation
 */
$installer->endSetup();
