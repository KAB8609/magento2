<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->unregister('website');
$objectManager->get('Magento_Core_Model_Registry')->unregister('store_group');
$objectManager->get('Magento_Core_Model_Registry')->unregister('store_on_new_website');
