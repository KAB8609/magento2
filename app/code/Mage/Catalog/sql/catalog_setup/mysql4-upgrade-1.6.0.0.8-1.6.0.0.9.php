<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installFile = dirname(__FILE__) . DS . 'upgrade-1.6.0.0.8-1.6.0.0.9.php';
if (file_exists($installFile)) {
    include $installFile;
}

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
/** @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $installer->getConnection();
$memoryTables = array(
    'catalog_category_anc_categs_index_tmp',
    'catalog_category_anc_products_index_tmp',
    'catalog_category_product_index_enbl_tmp',
    'catalog_category_product_index_tmp',
    'catalog_product_index_eav_decimal_tmp',
    'catalog_product_index_eav_tmp',
    'catalog_product_index_price_cfg_opt_agr_tmp',
    'catalog_product_index_price_cfg_opt_tmp',
    'catalog_product_index_price_final_tmp',
    'catalog_product_index_price_opt_agr_tmp',
    'catalog_product_index_price_opt_tmp',
    'catalog_product_index_price_tmp',
);

foreach ($memoryTables as $table) {
    $connection->changeTableEngine($installer->getTable($table), Varien_Db_Adapter_Pdo_Mysql::ENGINE_MEMORY);
}
