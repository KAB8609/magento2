<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer Magento_Reminder_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'magento_reminder_rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reminder_rule'))
    ->addColumn('rule_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('name', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Name')
    ->addColumn('description', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('conditions_serialized', Magento_DB_Ddl_Table::TYPE_TEXT, '2M', array(
        'nullable'  => false,
        ), 'Conditions Serialized')
    ->addColumn('condition_sql', Magento_DB_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Condition Sql')
    ->addColumn('is_active', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Active')
    ->addColumn('salesrule_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Salesrule Id')
    ->addColumn('schedule', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Schedule')
    ->addColumn('default_label', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Default Label')
    ->addColumn('default_description', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Default Description')
    ->addColumn('active_from', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Active From')
    ->addColumn('active_to', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Active To')
    ->addIndex($installer->getIdxName('magento_reminder_rule', array('salesrule_id')),
        array('salesrule_id'))
    ->addForeignKey($installer->getFkName('magento_reminder_rule', 'salesrule_id', 'salesrule', 'rule_id'),
        'salesrule_id', $installer->getTable('salesrule'), 'rule_id',
        Magento_DB_Ddl_Table::ACTION_SET_NULL, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reminder Rule');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reminder_rule_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reminder_rule_website'))
    ->addColumn('rule_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('website_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addIndex($installer->getIdxName('magento_reminder_rule_website', array('website_id')),
        array('website_id'))
    ->addForeignKey(
        $installer->getFkName('magento_reminder_rule_website', 'rule_id', 'magento_reminder_rule', 'rule_id'),
        'rule_id', $installer->getTable('magento_reminder_rule'), 'rule_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reminder Rule Website');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reminder_template'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reminder_template'))
    ->addColumn('rule_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Store Id')
    ->addColumn('template_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Template Id')
    ->addColumn('label', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Label')
    ->addColumn('description', Magento_DB_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addIndex($installer->getIdxName('magento_reminder_template', array('rule_id')),
        array('rule_id'))
    ->addIndex($installer->getIdxName('magento_reminder_template', array('template_id')),
        array('template_id'))
    ->addForeignKey(
        $installer->getFkName('magento_reminder_template', 'template_id', 'core_email_template', 'template_id'),
        'template_id', $installer->getTable('core_email_template'), 'template_id',
        Magento_DB_Ddl_Table::ACTION_SET_NULL, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('magento_reminder_template', 'rule_id', 'magento_reminder_rule', 'rule_id'),
        'rule_id', $installer->getTable('magento_reminder_rule'), 'rule_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reminder Template');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reminder_rule_coupon'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reminder_rule_coupon'))
    ->addColumn('rule_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('coupon_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Coupon Id')
    ->addColumn('customer_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Id')
    ->addColumn('associated_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Associated At')
    ->addColumn('emails_failed', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Emails Failed')
    ->addColumn('is_active', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Active')
    ->addIndex($installer->getIdxName('magento_reminder_rule_coupon', array('rule_id')),
        array('rule_id'))
    ->addForeignKey(
        $installer->getFkName('magento_reminder_rule_coupon', 'rule_id', 'magento_reminder_rule', 'rule_id'),
        'rule_id', $installer->getTable('magento_reminder_rule'), 'rule_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reminder Rule Coupon');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_reminder_rule_log'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_reminder_rule_log'))
    ->addColumn('log_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Log Id')
    ->addColumn('rule_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Rule Id')
    ->addColumn('customer_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Customer Id')
    ->addColumn('sent_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Sent At')
    ->addIndex($installer->getIdxName('magento_reminder_rule_log', array('rule_id')),
        array('rule_id'))
    ->addIndex($installer->getIdxName('magento_reminder_rule_log', array('customer_id')),
        array('customer_id'))
    ->addForeignKey($installer->getFkName('magento_reminder_rule_log', 'rule_id', 'magento_reminder_rule', 'rule_id'),
        'rule_id', $installer->getTable('magento_reminder_rule'), 'rule_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reminder Rule Log');
$installer->getConnection()->createTable($table);


$installer->endSetup();