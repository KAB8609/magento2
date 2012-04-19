<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $entityType Mage_Eav_Model_Entity_Type */
$entityType = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
$taxClasses = Mage::getResourceModel('tax/class_collection')->toArray();
$taxClass = reset($taxClasses['items']);

return array(
    'type_id' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'attribute_set_id' => $entityType->getDefaultAttributeSetId(),
    'sku' => 'simple' . uniqid(),
    'name' => 'Test',
    'description' => 'Test description',
    'short_description' => 'Test short description',
    'weight' => 125,
    'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
    'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
    'price' => 25.50,
    'tax_class_id' => $taxClass['class_id'],
    // Field should not be validated if "Use Config Settings" checkbox is set
    // thus invalid value should not raise error
    'stock_data' => array(
        'manage_stock' => 1,
        'use_config_manage_stock' => 0,
        'qty' => 1,
        'min_qty' => -1,
        'use_config_min_qty' => 1,
        'min_sale_qty' => -1,
        'use_config_min_sale_qty' => 1,
        'max_sale_qty' => -1,
        'use_config_max_sale_qty' => 1,
        'is_qty_decimal' => 0,
        'backorders' => -1,
        'use_config_backorders' => 1,
        'notify_stock_qty' => 'text',
        'use_config_notify_stock_qty' => 1,
        'enable_qty_increments' => -100,
        'use_config_enable_qty_increments' => 1,
        'is_in_stock' => 0
    )
);
