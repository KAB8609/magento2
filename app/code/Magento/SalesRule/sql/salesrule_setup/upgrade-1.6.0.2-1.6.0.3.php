<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer           = $this;
$connection          = $installer->getConnection();

$rulesTable          = $installer->getTable('salesrule');
$websitesTable       = $installer->getTable('core_website');
$customerGroupsTable = $installer->getTable('customer_group');
$rulesWebsitesTable  = $installer->getTable('salesrule_website');
$rulesCustomerGroupsTable  = $installer->getTable('salesrule_customer_group');

$installer->startSetup();
/**
 * Create table 'salesrule_website' if not exists. This table will be used instead of
 * column website_ids of main catalog rules table
 */
$table = $connection->newTable($rulesWebsitesTable)
    ->addColumn('rule_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ),
        'Rule Id'
        )
    ->addColumn('website_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ),
        'Website Id'
    )
    ->addIndex(
        $installer->getIdxName('salesrule_website', array('rule_id')),
        array('rule_id')
    )
    ->addIndex(
        $installer->getIdxName('salesrule_website', array('website_id')),
        array('website_id')
    )
    ->addForeignKey($installer->getFkName('salesrule_website', 'rule_id', 'salesrule', 'rule_id'),
        'rule_id', $rulesTable, 'rule_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey($installer->getFkName('salesrule_website', 'website_id', 'core/website', 'website_id'),
        'website_id', $websitesTable, 'website_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Sales Rules To Websites Relations');

$connection->createTable($table);


/**
 * Create table 'salesrule_customer_group' if not exists. This table will be used instead of
 * column customer_group_ids of main catalog rules table
 */
$table = $connection->newTable($rulesCustomerGroupsTable)
    ->addColumn('rule_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ),
        'Rule Id'
    )
    ->addColumn('customer_group_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ),
        'Customer Group Id'
    )
    ->addIndex(
        $installer->getIdxName('salesrule_customer_group', array('rule_id')),
        array('rule_id')
    )
    ->addIndex(
        $installer->getIdxName('salesrule_customer_group', array('customer_group_id')),
        array('customer_group_id')
    )
    ->addForeignKey($installer->getFkName('salesrule_customer_group', 'rule_id', 'salesrule', 'rule_id'),
        'rule_id', $rulesTable, 'rule_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('salesrule_customer_group', 'customer_group_id',
            'customer_group', 'customer_group_id'
        ),
        'customer_group_id', $customerGroupsTable, 'customer_group_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Sales Rules To Customer Groups Relations');

$connection->createTable($table);


/**
 * Fill out relation table 'salesrule_website' with website Ids
 */
$select = $connection->select()
    ->from(array('sr' => $rulesTable), array('sr.rule_id', 'cw.website_id'))
    ->join(
        array('cw' => $websitesTable),
        $connection->prepareSqlCondition(
           'sr.website_ids', array('finset' =>  new Zend_Db_Expr('cw.website_id'))
        ),
        array()
    );
$query = $select->insertFromSelect($rulesWebsitesTable, array('rule_id', 'website_id'));
$connection->query($query);


/**
 * Fill out relation table 'salesrule_customer_group' with customer group Ids
 */

$select = $connection->select()
    ->from(array('sr' => $rulesTable), array('sr.rule_id', 'cg.customer_group_id'))
    ->join(
        array('cg' => $customerGroupsTable),
        $connection->prepareSqlCondition(
            'sr.customer_group_ids', array('finset' =>  new Zend_Db_Expr('cg.customer_group_id'))
        ),
        array()
    );
$query = $select->insertFromSelect($rulesCustomerGroupsTable, array('rule_id', 'customer_group_id'));
$connection->query($query);

/**
 * Eliminate obsolete columns
 */
$connection->dropColumn($rulesTable, 'website_ids');
$connection->dropColumn($rulesTable, 'customer_group_ids');

/**
 * Change default value to "null" for "from" and "to" dates columns
 */
$connection->modifyColumn(
    $rulesTable,
    'from_date',
    array(
        'type'      => Magento_DB_Ddl_Table::TYPE_DATE,
        'nullable'  => true,
        'default'   => null
    )
);

$connection->modifyColumn(
    $rulesTable,
    'to_date',
    array(
        'type'      => Magento_DB_Ddl_Table::TYPE_DATE,
        'nullable'  => true,
        'default'   => null
    )
);

$installer->endSetup();