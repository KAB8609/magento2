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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Customer_Model_Entity_Setup */

$installer->startSetup();

$installer->run("
drop table if exists `design_change`;
CREATE TABLE `design_change` (
`design_change_id` INT NOT NULL AUTO_INCREMENT,
`store_id` INT NOT NULL ,
`package` VARCHAR( 255 ) NOT NULL ,
`theme` VARCHAR( 255 ) NOT NULL ,
`date_from` DATE NOT NULL ,
`date_to` DATE NOT NULL,
PRIMARY KEY  (`design_change_id`)
) ENGINE = innodb;
");

$installer->endSetup();