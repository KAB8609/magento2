<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sitemap
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

// Copy images to tmp media path
/** @var Mage_Catalog_Model_Product_Media_Config $config */
$config = Mage::getSingleton('Mage_Catalog_Model_Product_Media_Config');
$baseTmpMediaPath = $config->getBaseTmpMediaPath();

/** @var Magento_Filesystem $filesystem */
$filesystem = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Filesystem');
$filesystem->setIsAllowCreateDirectories(true);
$filesystem->copy(dirname(__FILE__) . '/magento_image_sitemap.png', $baseTmpMediaPath . '/magento_image_sitemap.png');
$filesystem->copy(dirname(__FILE__) . '/second_image.png', $baseTmpMediaPath . '/second_image.png');

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setName('Simple Product Enabled')
    ->setSku('simple_no_images')
    ->setPrice(10)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setWebsiteIds(array(1))
    ->setStockData(array('qty' => 100, 'is_in_stock' => 1))
    ->save();

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(2)
    ->setAttributeSetId(4)
    ->setName('Simple Product Invisible')
    ->setSku('simple_invisible')
    ->setPrice(10)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setWebsiteIds(array(1))
    ->setStockData(array('qty' => 100, 'is_in_stock' => 1))
    ->setRelatedLinkData(array(1 => array('position' => 1)))
    ->save();

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(3)
    ->setAttributeSetId(4)
    ->setName('Simple Product Disabled')
    ->setSku('simple_disabled')
    ->setPrice(10)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
    ->setWebsiteIds(array(1))
    ->setStockData(array('qty' => 100, 'is_in_stock' => 1))
    ->setRelatedLinkData(array(1 => array('position' => 1)))
    ->save();

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(4)
    ->setAttributeSetId(4)
    ->setName('Simple Images')
    ->setSku('simple_with_images')
    ->setPrice(10)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setImage('/s/e/second_image.png')
    ->setSmallImage('/m/a/magento_image_sitemap.png')
    ->setThumbnail('/m/a/magento_image_sitemap.png')
    ->addImageToMediaGallery($baseTmpMediaPath . '/magento_image_sitemap.png', null, false, false)
    ->addImageToMediaGallery($baseTmpMediaPath . '/second_image.png', null, false, false)
    ->setWebsiteIds(array(1))
    ->setStockData(array('qty' => 100, 'is_in_stock' => 1))
    ->setRelatedLinkData(array(1 => array('position' => 1)))
    ->save();

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(5)
    ->setAttributeSetId(4)
    ->setName('Simple Images')
    ->setSku('simple_with_images')
    ->setPrice(10)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setImage('no_selection')
    ->setSmallImage('/m/a/magento_image_sitemap.png')
    ->setThumbnail('no_selection')
    ->addImageToMediaGallery($baseTmpMediaPath . '/second_image.png', null, false, false)
    ->setWebsiteIds(array(1))
    ->setStockData(array('qty' => 100, 'is_in_stock' => 1))
    ->setRelatedLinkData(array(1 => array('position' => 1)))
    ->save();
