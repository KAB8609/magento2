<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer Magento_Catalog_Model_Resource_Setup */

$fieldList = array(
    'price','special_price','special_from_date','special_to_date',
    'minimal_price','cost','tier_price','weight'
);
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute(Magento_Catalog_Model_Product::ENTITY, $field, 'apply_to'));
    if (!in_array('bundle', $applyTo)) {
        $applyTo[] = 'bundle';
        $installer->updateAttribute(Magento_Catalog_Model_Product::ENTITY, $field, 'apply_to', implode(',', $applyTo));
    }
}

$applyTo = explode(',', $installer->getAttribute(Magento_Catalog_Model_Product::ENTITY, 'cost', 'apply_to'));
unset($applyTo[array_search('bundle', $applyTo)]);
$installer->updateAttribute(Magento_Catalog_Model_Product::ENTITY, 'cost', 'apply_to', implode(',', $applyTo));
