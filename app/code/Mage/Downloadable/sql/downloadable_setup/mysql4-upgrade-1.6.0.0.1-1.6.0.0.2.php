<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
$installFile = dirname(__FILE__) . DS . 'upgrade-1.6.0.0.1-1.6.0.0.2.php';
if (file_exists($installFile)) {
    include $installFile;
}

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
/** @var $connection Magento_DB_Adapter_Pdo_Mysql */
$connection = $installer->getConnection();
$connection->changeTableEngine(
    $installer->getTable('catalog_product_index_price_downlod_tmp'),
    Magento_DB_Adapter_Pdo_Mysql::ENGINE_MEMORY
);
