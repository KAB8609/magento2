<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'widget/widget'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('widget/widget'))
    ->addColumn('widget_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Widget Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Widget code for template directive')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Block Model')
    ->addColumn('parameters', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
        ), 'Parameters')
    ->addIndex($installer->getIdxName('widget/widget', 'code'), 'code')
    ->setComment('Preconfigured Widgets');
$installer->getConnection()->createTable($table);

/**
 * Create table 'widget/widget_instance'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('widget/widget_instance'))
    ->addColumn('instance_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Instance Id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Block Model')
    ->addColumn('package_theme', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Package Theme')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Widget Title')
    ->addColumn('store_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ids')
    ->addColumn('widget_parameters', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Widget parameters')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort order')
    ->setComment('Instances of Widget for Package Theme');
$installer->getConnection()->createTable($table);

/**
 * Create table 'widget/widget_instance_page'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('widget/widget_instance_page'))
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Page Id')
    ->addColumn('instance_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Instance Id')
    ->addColumn('group', Varien_Db_Ddl_Table::TYPE_TEXT, 25, array(
        ), 'Block Group Type')
    ->addColumn('layout_handle', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Layout Handle')
    ->addColumn('block_reference', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Block Reference')
    ->addColumn('for', Varien_Db_Ddl_Table::TYPE_TEXT, 25, array(
        ), 'For instance entities')
    ->addColumn('entities', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Catalog entities (comma separated)')
    ->addColumn('template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Path to widget template')
    ->addIndex($installer->getIdxName('widget/widget_instance_page', 'instance_id'), 'instance_id')
    ->addForeignKey($installer->getFkName('widget/widget_instance_page', 'instance_id', 'widget/widget_instance', 'instance_id'),
        'instance_id', $installer->getTable('widget/widget_instance'), 'instance_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Instance of Widget on Page');
$installer->getConnection()->createTable($table);

/**
 * Create table 'widget/widget_instance_page_layout'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('widget/widget_instance_page_layout'))
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Page Id')
    ->addColumn('layout_update_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Layout Update Id')
    ->addIndex($installer->getIdxName('widget/widget_instance_page_layout', 'page_id'), 'page_id')
    ->addIndex($installer->getIdxName('widget/widget_instance_page_layout', 'layout_update_id'), 'layout_update_id')
    ->addIndex($installer->getIdxName('widget/widget_instance_page_layout', array('layout_update_id', 'page_id'), true),
        array('layout_update_id', 'page_id'), array('unique' => true))
    ->addForeignKey($installer->getFkName('widget/widget_instance_page_layout', 'page_id', 'widget/widget_instance_page', 'page_id'),
        'page_id', $installer->getTable('widget/widget_instance_page'), 'page_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('widget/widget_instance_page_layout', 'layout_update_id', 'core/layout_update', 'layout_update_id'),
        'layout_update_id', $installer->getTable('core/layout_update'), 'layout_update_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Layout updates');
$installer->getConnection()->createTable($table);

$installer->endSetup();
