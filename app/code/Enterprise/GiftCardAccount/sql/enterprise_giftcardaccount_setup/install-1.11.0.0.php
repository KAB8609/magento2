<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer Enterprise_GiftCardAccount_Model_Resource_Setup */
$installer->startSetup();

/**
 * Create table 'enterprise_giftcardaccount'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_giftcardaccount'))
    ->addColumn('giftcardaccount_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Giftcardaccount Id')
    ->addColumn('code', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Code')
    ->addColumn('status', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        ), 'Status')
    ->addColumn('date_created', Magento_DB_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => false,
        ), 'Date Created')
    ->addColumn('date_expires', Magento_DB_Ddl_Table::TYPE_DATE, null, array(
        ), 'Date Expires')
    ->addColumn('website_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('balance', Magento_DB_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Balance')
    ->addColumn('state', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'State')
    ->addColumn('is_redeemable', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Redeemable')
    ->addIndex($installer->getIdxName('enterprise_giftcardaccount', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('enterprise_giftcardaccount', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Giftcardaccount');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_giftcardaccount_pool'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_giftcardaccount_pool'))
    ->addColumn('code', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Code')
    ->addColumn('status', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0'
        ), 'Status')
    ->setComment('Enterprise Giftcardaccount Pool');
$installer->getConnection()->createTable($table);

/**
 * Create table 'enterprise_giftcardaccount_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('enterprise_giftcardaccount_history'))
    ->addColumn('history_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'History Id')
    ->addColumn('giftcardaccount_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Giftcardaccount Id')
    ->addColumn('updated_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Updated At')
    ->addColumn('action', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Action')
    ->addColumn('balance_amount', Magento_DB_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Balance Amount')
    ->addColumn('balance_delta', Magento_DB_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Balance Delta')
    ->addColumn('additional_info', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Additional Info')
    ->addIndex($installer->getIdxName('enterprise_giftcardaccount_history', array('giftcardaccount_id')),
        array('giftcardaccount_id'))
    ->addForeignKey($installer->getFkName('enterprise_giftcardaccount_history', 'giftcardaccount_id', 'enterprise_giftcardaccount', 'giftcardaccount_id'),
        'giftcardaccount_id', $installer->getTable('enterprise_giftcardaccount'), 'giftcardaccount_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Enterprise Giftcardaccount History');
$installer->getConnection()->createTable($table);

// 0.0.1 => 0.0.2
$installer->addAttribute('quote', 'gift_cards', array('type'=>'text'));

// 0.0.2 => 0.0.3
$installer->addAttribute('quote', 'gift_cards_amount', array('type'=>'decimal'));
$installer->addAttribute('quote', 'base_gift_cards_amount', array('type'=>'decimal'));

$installer->addAttribute('quote_address', 'gift_cards_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_address', 'base_gift_cards_amount', array('type'=>'decimal'));

$installer->addAttribute('quote', 'gift_cards_amount_used', array('type'=>'decimal'));
$installer->addAttribute('quote', 'base_gift_cards_amount_used', array('type'=>'decimal'));

// 0.0.3 => 0.0.4
$installer->addAttribute('quote_address', 'gift_cards', array('type'=>'text'));

// 0.0.4 => 0.0.5
$installer->addAttribute('order', 'gift_cards', array('type'=>'text'));
$installer->addAttribute('order', 'base_gift_cards_amount', array('type'=>'decimal'));
$installer->addAttribute('order', 'gift_cards_amount', array('type'=>'decimal'));

// 0.0.5 => 0.0.6
$installer->addAttribute('quote_address', 'used_gift_cards', array('type'=>'text'));

// 0.0.9 => 0.0.9
$installer->addAttribute('order', 'base_gift_cards_invoiced', array('type'=>'decimal'));
$installer->addAttribute('order', 'gift_cards_invoiced', array('type'=>'decimal'));

$installer->addAttribute('invoice', 'base_gift_cards_amount', array('type'=>'decimal'));
$installer->addAttribute('invoice', 'gift_cards_amount', array('type'=>'decimal'));

// 0.0.11 => 0.0.12
$installer->addAttribute('order', 'base_gift_cards_refunded', array('type'=>'decimal'));
$installer->addAttribute('order', 'gift_cards_refunded', array('type'=>'decimal'));

$installer->addAttribute('creditmemo', 'base_gift_cards_amount', array('type'=>'decimal'));
$installer->addAttribute('creditmemo', 'gift_cards_amount', array('type'=>'decimal'));

$installer->endSetup();