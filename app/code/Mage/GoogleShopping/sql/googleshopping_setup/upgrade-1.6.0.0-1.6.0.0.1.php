<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('googleshopping_types'), 'category', array(
    'TYPE'    => Magento_DB_Ddl_Table::TYPE_TEXT,
    'LENGTH'  => 40,
    'COMMENT' => 'Google product category',
));