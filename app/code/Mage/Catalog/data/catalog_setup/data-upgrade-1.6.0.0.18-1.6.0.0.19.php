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

$attribute = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'image');

if ($attribute) {
    $this->addAttributeToGroup(
        $attribute['entity_type_id'],
        $this->getAttributeSetId($attribute['entity_type_id'], 'Default'),
        'General',
        $attribute['attribute_id'],
        0
    );

    $this->updateAttribute(
        $attribute['entity_type_id'],
        $attribute['attribute_id'],
        'frontend_input_renderer',
        'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_BaseImage'
    );
}
