<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$connection->update($installer->getTable('core_theme'), array('area' => 'frontend'), array('area = ?' => ''));

$installer->endSetup();
 \Mage::dispatchEvent('theme_registration_from_filesystem');
