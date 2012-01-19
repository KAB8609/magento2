<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer  = $this;
$connection = $installer->getConnection();

/**
 * Create table 'catalog_product_entity_group_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog_product_entity_group_price'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('all_groups', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Applicable To All Customer Groups')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Group ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Value')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Website ID')
    ->addIndex(
        $installer->getIdxName(
            'catalog_product_entity_group_price',
            array('entity_id', 'all_groups', 'customer_group_id', 'website_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'all_groups', 'customer_group_id', 'website_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('catalog_product_entity_group_price', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('catalog_product_entity_group_price', array('customer_group_id')),
        array('customer_group_id'))
    ->addIndex($installer->getIdxName('catalog_product_entity_group_price', array('website_id')),
        array('website_id'))
    ->addForeignKey(
        $installer->getFkName(
            'catalog_product_entity_group_price',
            'customer_group_id',
            'customer_group',
            'customer_group_id'
        ),
        'customer_group_id', $installer->getTable('customer_group'), 'customer_group_id',
         Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'catalog_product_entity_group_price',
            'entity_id',
            'catalog_product_entity',
            'entity_id'
        ),
        'entity_id', $installer->getTable('catalog_product_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'catalog_product_entity_group_price',
            'website_id',
            'core_website',
            'website_id'
        ),
        'website_id', $installer->getTable('core_website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Product Group Price Attribute Backend Table');
$installer->getConnection()->createTable($table);

$installer->addAttribute('catalog_product', 'group_price', array(
    'type'                       => 'decimal',
    'label'                      => 'Group Price',
    'input'                      => 'text',
    'backend'                    => 'Mage_Catalog_Model_Product_Attribute_Backend_Groupprice',
    'required'                   => false,
    'sort_order'                 => 6,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'apply_to'                   => 'simple,configurable,virtual',
    'group'                      => 'Prices',
));

/**
 * Create table 'catalog_product_index_group_price'
 */
$table = $connection
    ->newTable($installer->getTable('catalog_product_index_group_price'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addIndex($installer->getIdxName('catalog_product_index_group_price', array('customer_group_id')),
        array('customer_group_id'))
    ->addIndex($installer->getIdxName('catalog_product_index_group_price', array('website_id')),
        array('website_id'))
    ->addForeignKey(
        $installer->getFkName(
            'catalog_product_index_group_price',
            'customer_group_id',
            'customer_group',
            'customer_group_id'
        ),
        'customer_group_id', $installer->getTable('customer_group'), 'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'catalog_product_index_group_price',
            'entity_id',
            'catalog_product_entity',
            'entity_id'
        ),
        'entity_id', $installer->getTable('catalog_product_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'catalog_product_index_group_price',
            'website_id',
            'core_website',
            'website_id'
         ),
        'website_id', $installer->getTable('core_website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Product Group Price Index Table');
$connection->createTable($table);

$finalPriceIndexerTables = array(
    'catalog_product_index_price_final_idx',
    'catalog_product_index_price_final_tmp',
);

$priceIndexerTables =  array(
    'catalog_product_index_price_opt_agr_idx',
    'catalog_product_index_price_opt_agr_tmp',
    'catalog_product_index_price_opt_idx',
    'catalog_product_index_price_opt_tmp',
    'catalog_product_index_price_idx',
    'catalog_product_index_price_tmp',
    'catalog_product_index_price_cfg_opt_agr_idx',
    'catalog_product_index_price_cfg_opt_agr_tmp',
    'catalog_product_index_price_cfg_opt_idx',
    'catalog_product_index_price_cfg_opt_tmp',
    'catalog_product_index_price',
);

foreach ($finalPriceIndexerTables as $table) {
    $connection->addColumn($installer->getTable($table), 'group_price', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Group price',
    ));
    $connection->addColumn($installer->getTable($table), 'base_group_price', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Base Group Price',
    ));
}

foreach ($priceIndexerTables as $table) {
    $connection->addColumn($installer->getTable($table), 'group_price', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'comment'   => 'Group price',
    ));
}
