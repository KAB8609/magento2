<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!Mage::registry('attribute_set_with_configurable')) {
    define('ATTRIBUTES_COUNT', 2);
    define('ATTRIBUTE_OPTIONS_COUNT', 3);

    /** @var $entityType Mage_Eav_Model_Entity_Type */
    $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');

    /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
    $attributeSet = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set');
    $attributeSet->setEntityTypeId($entityType->getEntityTypeId())
        ->setAttributeSetName('Test Attribute Set ' . uniqid());

    $attributeSet->save();
    /** @var $entityType Mage_Eav_Model_Entity_Type */
    $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
    $attributeSet->initFromSkeleton($entityType->getDefaultAttributeSetId())->save();
    Mage::register('attribute_set_with_configurable', $attributeSet);

    /** @var $attributeFixture Mage_Catalog_Model_Resource_Eav_Attribute */
    $attributeFixture = Mage::getModel('Mage_Catalog_Model_Resource_Eav_Attribute');

    $attributeFixture->setEntityTypeId(Mage::getModel('Mage_Eav_Model_Entity')->setType('catalog_product')->getTypeId())
        ->setAttributeCode('test_attr_' . uniqid())
        ->setIsUserDefined(true)
        ->setIsVisibleOnFront(false)
        ->setIsRequired(false)
        ->setFrontendLabel(array(0 => 'Test Attr ' . uniqid()))
        ->setApplyTo(array());

    for ($attributeCount = 1; $attributeCount <= ATTRIBUTES_COUNT; $attributeCount++) {
        $attribute = clone $attributeFixture;
        $attribute->setAttributeCode('test_attr_' . uniqid())
            ->setFrontendLabel(array(0 => 'Test Attr ' . uniqid()))
            ->setIsGlobal(true)
            ->setIsConfigurable(true)
            ->setIsRequired(true)
            ->setFrontendInput('select')
            ->setBackendType('int')
            ->setAttributeSetId($attributeSet->getId())
            ->setAttributeGroupId($attributeSet->getDefaultGroupId());

        $options = array();
        for ($optionCount = 0; $optionCount < ATTRIBUTE_OPTIONS_COUNT; $optionCount++) {
            $options['option_' . $optionCount] = array(
                0 => 'Test Option #' . $optionCount
            );
        }
        $attribute->setOption(
            array(
                'value' => $options
            )
        );
        $attribute->save();
        Mage::register('eav_configurable_attribute_' . $attributeCount, $attribute);
        unset($attribute);
    }
}


