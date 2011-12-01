<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once '../../app/bootstrap.php';
Mage::app('admin', 'store');

/** @var $shell Mage_Index_Model_Shell */
$shell = Mage::getModel('Mage_Index_Model_Shell', basename(__FILE__));
$shell->run();
