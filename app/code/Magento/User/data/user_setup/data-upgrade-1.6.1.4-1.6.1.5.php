<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('admin_rule');

if ($tableName) {
    /** @var \Magento\DB\Adapter\AdapterInterface $connection */
    $connection = $installer->getConnection();
    $connection->delete($tableName, array('resource_id = ?' => 'Magento_Oauth::oauth'));
}

$installer->endSetup();
