<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', array('resourceName' => 'core_setup'));
$installer->startSetup();

$attributeData = $this->getAttribute('catalog_category', 'custom_layout_update');
$installer->appendClassAliasReplace('catalog_category_entity_text', 'value',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('value_id'),
    'attribute_id = ' . (int) $attributeData['attribute_id']
);

$attributeData = $this->getAttribute('catalog_product', 'custom_layout_update');
$installer->appendClassAliasReplace('catalog_product_entity_text', 'value',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('value_id'),
    'attribute_id = ' . (int) $attributeData['attribute_id']
);

$installer->appendClassAliasReplace('catalog_eav_attribute', 'frontend_input_renderer',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
