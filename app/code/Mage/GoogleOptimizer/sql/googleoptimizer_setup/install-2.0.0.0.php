<?php
/**
 * GoogleOptimizer install
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'googleoptimizer_code'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('googleoptimizer_code'))
    ->addColumn('code_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Google experiment code id')
    ->addColumn('entity_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Optimized entity id product id or catalog id')
    ->addColumn('entity_type', Magento_DB_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Optimized entity type')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store id')
    ->addColumn('experiment_script', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(), 'Google experiment script')
    ->addIndex($installer->getIdxName('googleoptimizer_code', array('store_id')), array('store_id'))
    ->addIndex($installer->getIdxName(
        'googleoptimizer_code',
        array('store_id', 'entity_id', 'entity_type'),
        Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
    ), array('store_id', 'entity_id', 'entity_type'), array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey($installer->getFkName('googleoptimizer_code', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Google Experiment code');
$installer->getConnection()->createTable($table);

$installer->endSetup();
