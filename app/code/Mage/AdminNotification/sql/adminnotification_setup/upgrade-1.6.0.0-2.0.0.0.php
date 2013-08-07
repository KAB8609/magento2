<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
/**
 * Create table 'admin_system_messages'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('admin_system_messages'))
    ->addColumn('identity', Magento_DB_Ddl_Table::TYPE_TEXT, 100, array(
        'nullable'  => false,
        'primary'   => true,
    ), 'Message id')
    ->addColumn('severity', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Problem type')
    ->addColumn('created_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Create date')
    ->setComment('Admin System Messages');
$installer->getConnection()->createTable($table);

$installer->endSetup();
