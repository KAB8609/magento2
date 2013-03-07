<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer Enterprise_Reminder_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'enterprise_reminder_rule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_reminder_rule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        'nullable'  => false,
        ), 'Conditions Serialized')
    ->addColumn('condition_sql', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Condition Sql')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Active')
    ->addColumn('salesrule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Salesrule Id')
    ->addColumn('schedule', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Schedule')
    ->addColumn('default_label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Default Label')
    ->addColumn('default_description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Default Description')
    ->addColumn('active_from', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Active From')
    ->addColumn('active_to', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Active To')
    ->addIndex($installer->getIdxName('enterprise_reminder_rule', array('salesrule_id')),
        array('salesrule_id'))
    ->addForeignKey($installer->getFkName('enterprise_reminder_rule', 'salesrule_id', 'salesrule', 'rule_id'),
        'salesrule_id', $installer->getTable('salesrule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reminder Rule');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_reminder_rule_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_reminder_rule_website'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addIndex($installer->getIdxName('enterprise_reminder_rule_website', array('website_id')),
        array('website_id'))
    ->addForeignKey(
        $installer->getFkName('enterprise_reminder_rule_website', 'rule_id', 'enterprise_reminder_rule', 'rule_id'),
        'rule_id', $installer->getTable('enterprise_reminder_rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reminder Rule Website');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_reminder_template'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_reminder_template'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Store Id')
    ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Template Id')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Label')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addIndex($installer->getIdxName('enterprise_reminder_template', array('rule_id')),
        array('rule_id'))
    ->addIndex($installer->getIdxName('enterprise_reminder_template', array('template_id')),
        array('template_id'))
    ->addForeignKey(
        $installer->getFkName('enterprise_reminder_template', 'template_id', 'core_email_template', 'template_id'),
        'template_id', $installer->getTable('core_email_template'), 'template_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('enterprise_reminder_template', 'rule_id', 'enterprise_reminder_rule', 'rule_id'),
        'rule_id', $installer->getTable('enterprise_reminder_rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reminder Template');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_reminder_rule_coupon'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_reminder_rule_coupon'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule Id')
    ->addColumn('coupon_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Coupon Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Id')
    ->addColumn('associated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Associated At')
    ->addColumn('emails_failed', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Emails Failed')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Active')
    ->addIndex($installer->getIdxName('enterprise_reminder_rule_coupon', array('rule_id')),
        array('rule_id'))
    ->addForeignKey(
        $installer->getFkName('enterprise_reminder_rule_coupon', 'rule_id', 'enterprise_reminder_rule', 'rule_id'),
        'rule_id', $installer->getTable('enterprise_reminder_rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reminder Rule Coupon');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_reminder_rule_log'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_reminder_rule_log'))
    ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Log Id')
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Rule Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Customer Id')
    ->addColumn('sent_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Sent At')
    ->addIndex($installer->getIdxName('enterprise_reminder_rule_log', array('rule_id')),
        array('rule_id'))
    ->addIndex($installer->getIdxName('enterprise_reminder_rule_log', array('customer_id')),
        array('customer_id'))
    ->addForeignKey($installer->getFkName('enterprise_reminder_rule_log', 'rule_id', 'enterprise_reminder_rule', 'rule_id'),
        'rule_id', $installer->getTable('enterprise_reminder_rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Reminder Rule Log');
$installer->getConnection()->createTable($table);


$installer->endSetup();
