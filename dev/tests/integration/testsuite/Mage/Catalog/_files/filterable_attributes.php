<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('Mage_Catalog_Model_Resource_Setup', array('resourceName' => 'catalog_setup'));
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityModel = Mage::getModel('Mage_Eav_Model_Entity');
$entityTypeId = $entityModel->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId();
$groupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

/** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
$attribute = Mage::getResourceModel('Mage_Catalog_Model_Resource_Eav_Attribute');
$attribute->setAttributeCode('filterable_attribute_a')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setIsFilterable(1)
    ->setIsUserDefined(1)
    ->save();

$attribute = Mage::getResourceModel('Mage_Catalog_Model_Resource_Eav_Attribute');
$attribute->setAttributeCode('filterable_attribute_b')
    ->setEntityTypeId($entityTypeId)
    ->setAttributeGroupId($groupId)
    ->setAttributeSetId($attributeSetId)
    ->setIsFilterable(1)
    ->setIsUserDefined(1)
    ->save();
