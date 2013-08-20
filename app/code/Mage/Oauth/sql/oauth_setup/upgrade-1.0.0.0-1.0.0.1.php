<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->dropIndex($installer->getTable('oauth_nonce'), $installer->getIdxName(
        'oauth_nonce',
        array('nonce'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ));

$installer
    ->getConnection()
    ->addColumn(
        $installer->getTable('oauth_nonce'),
        'consumer_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned' => true,
            'nullable' => false,
            'comment' => 'Consumer ID'
        ));

$keyFieldsList = array('nonce', 'consumer_id');
$installer
    ->getConnection()
    ->addIndex(
        $installer->getTable('oauth_nonce'),
        $installer->getIdxName(
            'oauth_nonce',
            $keyFieldsList,
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        $keyFieldsList,
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );

$installer
    ->getConnection()
    ->addForeignKey(
        $installer->getFkName('oauth_nonce', 'consumer_id', 'oauth_consumer', 'entity_id'),
        $installer->getTable('oauth_nonce'),
        'consumer_id',
        $installer->getTable('oauth_consumer'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$installer
    ->getConnection()
    ->addColumn(
        $installer->getTable('oauth_consumer'),
        'http_post_url',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => false,
            'comment' => 'Http Post URL'
        )
    );

$installer->endSetup();
