<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleShopping install
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$table = $connection->newTable($this->getTable('googleshopping_types'))
    ->addColumn('type_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true
        ), 'Type ID')
    ->addColumn('attribute_set_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false
        ), 'Attribute Set Id')
    ->addColumn('target_country', Magento_DB_Ddl_Table::TYPE_TEXT, 2, array(
        'nullable' => false,
        'default' => 'US'
        ), 'Target country')
    ->addForeignKey(
        $installer->getFkName(
            'googleshopping_types',
            'attribute_set_id',
            'eav_attribute_set',
            'attribute_set_id'
        ),
        'attribute_set_id',
        $this->getTable('eav_attribute_set'),
        'attribute_set_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addIndex(
        $installer->getIdxName(
            'googleshopping_types',
            array('attribute_set_id', 'target_country'),
            Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('attribute_set_id', 'target_country'),
        array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('Google Content Item Types link Attribute Sets');
$installer->getConnection()->createTable($table);

$table = $connection->newTable($this->getTable('googleshopping_items'))
    ->addColumn('item_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable' => false,
        'unsigned' => true,
        'primary' => true
        ), 'Item Id')
    ->addColumn('type_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true,
        'default' => 0
        ), 'Type Id')
    ->addColumn('product_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true
        ), 'Product Id')
    ->addColumn('gcontent_item_id', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
        ), 'Google Content Item Id')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false,
        'unsigned' => true
        ), 'Store Id')
    ->addColumn('published', Magento_DB_Ddl_Table::TYPE_DATETIME, null, array(), 'Published date')
    ->addColumn('expires', Magento_DB_Ddl_Table::TYPE_DATETIME, null, array(), 'Expires date')
    ->addForeignKey(
        $installer->getFkName(
            'googleshopping_items',
            'product_id',
            'catalog_product_entity',
            'entity_id'
        ),
        'product_id',
        $this->getTable('catalog_product_entity'),
        'entity_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE
     )
    ->addForeignKey(
        $installer->getFkName(
            'googleshopping_items',
            'store_id',
            'core_store',
            'store_id'
        ),
        'store_id',
        $this->getTable('core_store'),
        'store_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE
     )
    ->addIndex($installer->getIdxName('googleshopping_items', array('product_id', 'store_id')),
         array('product_id', 'store_id'))
    ->setComment('Google Content Items Products');
$installer->getConnection()->createTable($table);

$table = $connection->newTable($this->getTable('googleshopping_attributes'))
    ->addColumn('id', Magento_DB_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'nullable' => false,
        'unsigned' => true,
        'primary' => true
        ), 'Id')
    ->addColumn('attribute_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false,
        'unsigned' => true
        ), 'Attribute Id')
    ->addColumn('gcontent_attribute', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
        ), 'Google Content Attribute')
    ->addColumn('type_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true
        ), 'Type Id')
    ->addForeignKey(
        $installer->getFkName(
            'googleshopping_attributes',
            'attribute_id',
            'eav_attribute',
            'attribute_id'
        ),
        'attribute_id',
        $this->getTable('eav_attribute'),
        'attribute_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE
     )
    ->addForeignKey(
        $installer->getFkName(
            'googleshopping_attributes',
            'type_id',
            'googleshopping_types',
            'type_id'
        ),
        'type_id',
        $this->getTable('googleshopping_types'),
        'type_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE
     )
     ->setComment('Google Content Attributes link Product Attributes');
$installer->getConnection()->createTable($table);

$installer->endSetup();
