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

/**
 * Add column 'type' to 'core_theme'
 */
$connection->addColumn($installer->getTable('core_theme'), 'type', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'nullable' => false,
    'comment'  => 'Theme type: 0:physical, 1:virtual, 2:staging'
));

/**
 * Rename table
 */
$wrongName = 'core_theme_files';
$rightName = 'core_theme_file';
if ($installer->tableExists($wrongName)) {
    $connection->renameTable($installer->getTable($wrongName), $installer->getTable($rightName));
}

$installer->endSetup();
