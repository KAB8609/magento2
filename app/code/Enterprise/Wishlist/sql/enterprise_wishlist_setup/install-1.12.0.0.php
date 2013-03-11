<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$tableName = $installer->getTable('wishlist');

$installer->getConnection()->dropForeignKey(
    $tableName,
    $installer->getFkName('wishlist', 'customer_id', 'customer_entity', 'entity_id')
);
$installer->getConnection()->dropIndex(
    $tableName,
    $installer->getIdxName('wishlist', 'customer_id', Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);

$installer->getConnection()->addIndex(
    $tableName,
    $installer->getIdxName('wishlist', 'customer_id', Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX),
    'customer_id',
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);
$installer->getConnection()->addForeignKey(
    $installer->getFkName('wishlist', 'customer_id', 'customer_entity', 'entity_id'),
    $tableName,
    'customer_id',
    $installer->getTable('customer_entity'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->getConnection()->addColumn($tableName, 'name', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'   => 255,
        'comment'  => 'Wishlist name',
        'default'  => null
    )
);

$installer->getConnection()->addColumn($tableName, 'visibility', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => true,
        'default'  => 0,
        'comment'  => 'Wish list visibility type'
    )
);
