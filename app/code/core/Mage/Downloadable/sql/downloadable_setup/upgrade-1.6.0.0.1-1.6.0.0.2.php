<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$applyTo = explode(',', $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'group_price', 'apply_to'));
if (!in_array(Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE, $applyTo)) {
    $applyTo[] = Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE;
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'group_price', 'apply_to', implode(',', $applyTo));
}
