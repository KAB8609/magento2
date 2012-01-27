<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Reminder
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$conditions = serialize(array());

$rule = new Enterprise_Reminder_Model_Rule;
$rule->setData(array(
    'name' => 'Rule 1',
    'description' => 'Rule 1 Desc',
    'conditions_serialized' => $conditions,
    'condition_sql' => 1,
    'is_active' => 1,
    'salesrule_id' => null,
    'schedule' => null,
    'default_label' => null,
    'default_description' => null,
    'from_date' => null,
    'to_date' => '1981-01-01',
))->save();

$rule = new Enterprise_Reminder_Model_Rule;
$rule->setData(array(
    'name' => 'Rule 2',
    'description' => 'Rule 2 Desc',
    'conditions_serialized' => $conditions,
    'condition_sql' => 1,
    'is_active' => 1,
    'salesrule_id' => null,
    'schedule' => null,
    'default_label' => null,
    'default_description' => null,
    'from_date' => null,
    /**
     * For some reason any values in columns from_date and to_date are ignored
     * This has to be fixed in scope of MAGE-5166
     *
     * Also make sure that dates will be properly formatted through Varien_Db_Adapter_*::formatDate()
     */
    'to_date' => date('Y-m-d', time() + 172800),
))->save();

//$adapter = $rule->getResource()->getReadConnection();
//print_r($adapter->fetchAll('SELECT * FROM enterprise_reminder_rule'));
