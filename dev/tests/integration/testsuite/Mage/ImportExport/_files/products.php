<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
$products = array();
$product = new Mage_Catalog_Model_Product();
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(10)
    ->setAttributeSetId(4)
    ->setName('Simple Product 1')
    ->setSku('simple_product_1')
    ->setDescription('description')
    ->setShortDescription('short description')
    ->setOptionsContainer('container1')
    ->setMsrpDisplayActualPriceType(
        Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type::TYPE_IN_CART
    )
    ->setPrice(10)
    ->setWeight(1)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setWebsiteIds(array(1))
    ->setCateroryIds(array())
    ->setStockData(
        array(
            'use_config_manage_stock'   => 1,
            'qty'                       => 100,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        )
    )
    ->save();
$products[] = $product;

$product = new Mage_Catalog_Model_Product();
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(11)
    ->setAttributeSetId(4)
    ->setName('Simple Product 2')
    ->setSku('simple_product_2')
    ->setDescription('description')
    ->setShortDescription('short description')
    ->setEnableGooglecheckout(false)
    ->setOptionsContainer('container1')
    ->setMsrpEnabled(
        Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_YES
    )
    ->setMsrpDisplayActualPriceType(
        Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type::TYPE_ON_GESTURE
    )
    ->setPrice(20)
    ->setWeight(1)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setWebsiteIds(array(1))
    ->setCateroryIds(array())
    ->setStockData(
        array(
            'use_config_manage_stock'   => 1,
            'qty'                       => 120,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        )
    )
    ->save();
$products[] = $product;

$product = new Mage_Catalog_Model_Product();
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(12)
    ->setAttributeSetId(4)
    ->setName('Simple Product 3')
    ->setSku('simple_product_3')
    ->setDescription('description')
    ->setShortDescription('short description')
    ->setMsrpEnabled(
        Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_NO
    )
    ->setEnableGooglecheckout(false)
    ->setPrice(30)
    ->setWeight(1)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
    ->setWebsiteIds(array(1))
    ->setCateroryIds(array())
    ->setStockData(
        array(
            'use_config_manage_stock'   => 1,
            'qty'                       => 140,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        )
    )
    ->save();
$products[] = $product;
Mage::unregister('_fixture/Mage_ImportExport_Product_Collection');
Mage::register('_fixture/Mage_ImportExport_Product_Collection', $products);
