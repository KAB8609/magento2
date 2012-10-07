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

$mediaDir = Mage::getSingleton('Mage_Catalog_Model_Product_Media_Config')->getBaseMediaPath();
$dir = $mediaDir . '/m/a';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
copy(__DIR__ . '/magento_image.jpg', $mediaDir . '/m/a/magento_image.jpg');
