<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
Mage::setCurrentArea('adminhtml');
/** @var $session Magento_DesignEditor_Model_Session */
$session = Mage::getModel('Magento_DesignEditor_Model_Session');
/** @var $auth Magento_Backend_Model_Auth */
$auth = Mage::getModel('Magento_Backend_Model_Auth');
$auth->setAuthStorage($session);
$auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
$session->activateDesignEditor();

/** @var $theme Magento_Core_Model_Theme */
$theme = Mage::getModel('Magento_Core_Model_Theme');
$theme->setData(array(
    'theme_code'           => 'blank',
    'area'                 => 'frontend',
    'parent_id'            => null,
    'theme_path'           => 'magento_blank',
    'theme_version'        => '2.0.0.0',
    'theme_title'          => 'Default',
    'preview_image'        => 'media/preview_image.jpg',
    'is_featured'          => '0'
));
$theme->save();
$session->setThemeId($theme->getId());
