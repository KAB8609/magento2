<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;

$installer->getConnection()
        ->modifyColumn($installer->getTable('enterprise_scheduled_operations'), 'force_import', array(
            'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default'  => '0'
        ));
