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

$installer->getConnection()->addColumn($installer->getTable('core_theme_files'), 'is_temporary', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'nullable' => false,
    'default'  => 0,
    'comment'  => 'Is Temporary File'
));

$installer->endSetup();
