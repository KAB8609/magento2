<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId('virtual')
    ->setAttributeSetId(4)
    ->setStoreId(0)
    ->setName('Simple Product')
    ->setSku('virtual-creditmemo-' . uniqid())
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setStockData(
    array(
        'use_config_manage_stock' => 1,
        'qty' => 100,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    )
);
$product->save();
Mage::register('product_virtual', $product);
