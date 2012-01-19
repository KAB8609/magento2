<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_Reminder_Model_Resource_Setup */
$installer = $this;

$ruleTable  = $installer->getTable('enterprise_reminder_rule');
$ruleWebsiteTable = $installer->getTable('enterprise_reminder_rule_website');
$coreWebsiteTable = $installer->getTable('core_website');
$connection = $installer->getConnection();

$installer->startSetup();

$connection->changeColumn(
    $ruleTable,
    'active_from',
    'from_date',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
        'nullable'  => true,
        'default'   => null
    )
);

$connection->changeColumn(
    $ruleTable,
    'active_to',
    'to_date',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
        'nullable'  => true,
        'default'   => null
    )
);


/**
 * Clean relations with not existing websites
 */
$selectWebsiteIds = $connection->select()
    ->from($coreWebsiteTable, 'website_id');
$websiteIds = $connection->fetchCol($selectWebsiteIds);
if (!empty($websiteIds)) {
    $connection->delete($ruleWebsiteTable, $connection->quoteInto('website_id NOT IN (?)', $websiteIds));
}

/**
 * Add foreign key for rule website table onto core website table
 */
$connection->addForeignKey(
    $installer->getFkName('enterprise_reminder_rule_website', 'website_id', 'core_website', 'website_id'),
    $ruleWebsiteTable,
    'website_id',
    $coreWebsiteTable,
    'website_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->endSetup();
