<?php
/**
 * Upgrade script for integration table.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var \Magento\Integration\Model\Resource\Setup $installer */
$installer = $this;
$installer->getConnection()->addColumn(
    $installer->getTable('integration'),
    'type',
    array(
        'type' => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned' => true,
        'nullable' => false,
        'default' => 0,
        'comment' => 'Integration type - manual or config file'
    )
);