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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Add user_type + resource_id + operation unique key for api2_acl_attribute table
 */
/** @var $installer Mage_Api2_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getTable('api2/acl_attribute');
$indexes = array('user_type', 'resource_id', 'operation');

$installer->getConnection()
    ->addIndex(
        $table,
        $installer->getIdxName($table, $indexes),
        $indexes,
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );

$installer->endSetup();
