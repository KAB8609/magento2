<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

$oldName = 'core_theme_files_link';
$newName = 'core_theme_file_update';

$oldTableName = $installer->getTable($oldName);

/**
 * Drop foreign key and index
 */
$connection->dropForeignKey(
    $oldTableName,
    $installer->getFkName($oldName, 'theme_id', 'core_theme', 'theme_id')
);
$connection->dropIndex(
    $oldTableName,
    $installer->getFkName($oldName, 'theme_id', 'core_theme', 'theme_id')
);

/**
 * Rename table
 */
if ($installer->tableExists($oldName)) {
    $connection->renameTable($installer->getTable($oldName), $installer->getTable($newName));
}

$newTableName = $installer->getTable($newName);

/**
 * Rename column
 */
$oldColumn = 'files_link_id';
$newColumn = 'file_update_id';
$connection->changeColumn($newTableName, $oldColumn, $newColumn, array(
    'type'     => Magento_DB_Ddl_Table::TYPE_INTEGER,
    'primary'  => true,
    'nullable' => false,
    'unsigned' => true,
    'comment'  => 'Customization file update id'
));

/**
 * Rename column
 */
$oldColumn = 'layout_link_id';
$newColumn = 'layout_update_id';
$connection->changeColumn($newTableName, $oldColumn, $newColumn, array(
    'type'     => Magento_DB_Ddl_Table::TYPE_INTEGER,
    'nullable' => false,
    'unsigned' => true,
    'comment'  => 'Theme layout update id'
));

/**
 * Add foreign keys and indexes
 */
$connection->addIndex(
    $newTableName,
    $installer->getIdxName($newTableName, 'theme_id', Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE),
    'theme_id',
    Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
);
$connection->addForeignKey(
    $installer->getFkName($newTableName, 'theme_id', 'core_theme', 'theme_id'),
    $newTableName,
    'theme_id',
    $installer->getTable('core_theme'),
    'theme_id',
    Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE
);
$connection->addIndex(
    $newTableName,
    $installer->getIdxName($newTableName, 'layout_update_id', Magento_DB_Adapter_Interface::INDEX_TYPE_INDEX),
    'layout_update_id',
    Magento_DB_Adapter_Interface::INDEX_TYPE_INDEX
);
$connection->addForeignKey(
    $installer->getFkName($newTableName, 'layout_update_id', 'core_layout_update', 'layout_update_id'),
    $newTableName,
    'layout_update_id',
    $installer->getTable('core_layout_update'),
    'layout_update_id',
    Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE
);

/**
 * Change data
 */
$select = $connection->select()
    ->from($newTableName)
    ->join(
        array('link' => $installer->getTable('core_layout_link')),
        sprintf('link.layout_link_id = %s.layout_update_id', $newTableName)
    );
$rows = $connection->fetchAll($select);
foreach ($rows as $row) {
    $connection->update(
        $newTableName,
        array('layout_update_id' => $row['layout_update_id']),
        'file_update_id = ' . $row['file_update_id']
    );
}

$installer->endSetup();
