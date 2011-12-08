<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_shipment'), 'packages', array(
        'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Packed Products in Packages',
        'length'  => '20000'
    ));
$installer->endSetup();
