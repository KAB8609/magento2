<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Magento_Core_Model_Resource_Setup_Migration', array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('newsletter_template', 'template_text',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('template_id')
);
$installer->appendClassAliasReplace('newsletter_template', 'template_text_preprocessed',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('template_id')
);
$installer->appendClassAliasReplace('newsletter_queue', 'newsletter_text',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('queue_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
