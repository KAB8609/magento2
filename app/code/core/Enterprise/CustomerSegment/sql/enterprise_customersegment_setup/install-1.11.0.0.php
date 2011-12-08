<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Enterprise_CustomerSegment_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'enterprise_customersegment_segment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_customersegment_segment'))
    ->addColumn('segment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Segment Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Active')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Conditions Serialized')
    ->addColumn('processing_frequency', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Processing Frequency')
    ->addColumn('condition_sql', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Condition Sql')
    ->setComment('Enterprise Customersegment Segment');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_customersegment_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_customersegment_website'))
    ->addColumn('segment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Segment Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addIndex($installer->getIdxName('enterprise_customersegment_website', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('enterprise_customersegment_website', 'segment_id', 'enterprise_customersegment_segment', 'segment_id'),
        'segment_id', $installer->getTable('enterprise_customersegment_segment'), 'segment_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('enterprise_customersegment_website', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Customersegment Website');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_customersegment_customer'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_customersegment_customer'))
    ->addColumn('segment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Segment Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Id')
    ->addColumn('added_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Added Date')
    ->addColumn('updated_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Updated Date')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addIndex($installer->getIdxName('enterprise_customersegment_customer', array('segment_id', 'website_id', 'customer_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('segment_id', 'website_id', 'customer_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('enterprise_customersegment_customer', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('enterprise_customersegment_customer', array('customer_id')),
        array('customer_id'))
    ->addForeignKey($installer->getFkName('enterprise_customersegment_customer', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('enterprise_customersegment_customer', 'customer_id', 'customer_entity', 'entity_id'),
        'customer_id', $installer->getTable('customer_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('enterprise_customersegment_customer', 'segment_id', 'enterprise_customersegment_segment', 'segment_id'),
        'segment_id', $installer->getTable('enterprise_customersegment_segment'), 'segment_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Customersegment Customer');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_customersegment_event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_customersegment_event'))
    ->addColumn('segment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Segment Id')
    ->addColumn('event', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Event')
    ->addIndex($installer->getIdxName('enterprise_customersegment_event', array('event')),
        array('event'))
    ->addIndex($installer->getIdxName('enterprise_customersegment_event', array('segment_id')),
        array('segment_id'))
    ->addForeignKey($installer->getFkName('enterprise_customersegment_event', 'segment_id', 'enterprise_customersegment_segment', 'segment_id'),
        'segment_id', $installer->getTable('enterprise_customersegment_segment'), 'segment_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Customersegment Event');
$installer->getConnection()->createTable($table);

// add field that indicates that attribute is used for customer segments to attribute properties
$installer->getConnection()
    ->addColumn( $installer->getTable('customer_eav_attribute'), 'is_used_for_customer_segment', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        'comment'   => 'Customer Segment'
    ));

// use specific attributes for customer segments
$attributesOfEntities = array(
    'customer' => array('dob', 'email', 'firstname', 'group_id', 'lastname', 'gender', 'default_billing', 'default_shipping', 'created_at'),
    'customer_address' => array('firstname', 'lastname', 'company', 'street', 'city', 'region_id', 'postcode', 'country_id', 'telephone'),
    'order_address' => array('firstname', 'lastname', 'company', 'street', 'city', 'region_id', 'postcode', 'country_id', 'telephone', 'email'),
);

foreach ($attributesOfEntities as $entityTypeId => $attributes) {
    foreach ($attributes as $attributeCode){
        $installer->updateAttribute($entityTypeId, $attributeCode, 'is_used_for_customer_segment', '1');
    }
}

$installer->endSetup();
