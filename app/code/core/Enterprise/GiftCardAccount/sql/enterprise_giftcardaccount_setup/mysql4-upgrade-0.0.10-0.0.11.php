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
 * @category   Enterprise
 * @package    Enterprise_GiftCardAccount
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Enterprise_GiftCardAccount_Model_Mysql4_Setup */
$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_giftcardaccount/history')}` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `giftcardaccount_id` int(10) unsigned NOT NULL DEFAULT 0,
  `updated_at` datetime NULL DEFAULT NULL,
  `action` tinyint(3) unsigned NOT NULL default '0',
  `balance_amount` decimal(12,4) unsigned NOT NULL DEFAULT 0,
  `balance_delta` decimal(12,4) NOT NULL DEFAULT 0,
  `additional_info` tinytext COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`history_id`)
);
");

$installer->getConnection()->addConstraint(
    'FK_GIFTCARDACCOUNT_HISTORY_ACCOUNT',
    $installer->getTable('enterprise_giftcardaccount/history'), 'giftcardaccount_id',
    $installer->getTable('enterprise_giftcardaccount/giftcardaccount'), 'giftcardaccount_id'
);

$installer->endSetup();
