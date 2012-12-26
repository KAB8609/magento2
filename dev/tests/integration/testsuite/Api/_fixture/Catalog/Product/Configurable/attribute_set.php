<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!Magento_Test_TestCase_ApiAbstract::getFixture('attribute_set_with_configurable')) {
    define('ATTRIBUTES_COUNT', 2);
    define('ATTRIBUTE_OPTIONS_COUNT', 3);

    /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
    $attributeSet = require 'API/_fixture/_block/Catalog/Product/Attribute/Set.php';
    $attributeSet->save();
    /** @var $entityType Mage_Eav_Model_Entity_Type */
    $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
    $attributeSet->initFromSkeleton($entityType->getDefaultAttributeSetId())->save();
    Magento_Test_TestCase_ApiAbstract::setFixture('attribute_set_with_configurable', $attributeSet);

    /** @var $attributeFixture Mage_Catalog_Model_Resource_Eav_Attribute */
    $attributeFixture = require 'API/_fixture/_block/Catalog/Product/Attribute.php';

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
        Magento_Test_TestCase_ApiAbstract::setFixture('eav_configurable_attribute_' . $attributeCount, $attribute);
        unset($attribute);
    }
}


