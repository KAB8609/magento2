<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/* @var $stockItemFixture Mage_CatalogInventory_Model_Stock_Item */
$stockItemFixture = require '_fixture/_block/CatalogInventory/Stock/Item.php';

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setName('Simple Product')
    ->setSku('simple-product-' . microtime())
    ->setPrice(mt_rand(1, 100))
    ->setTaxClassId(0)
    ->setDescription('Product description')
    ->setShortDescription('Product short description')
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setStockItem($stockItemFixture);
return $product;
