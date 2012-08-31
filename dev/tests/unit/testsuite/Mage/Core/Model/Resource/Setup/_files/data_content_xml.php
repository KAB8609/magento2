<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    '$replaceRules' => array(
        array(
            'table',
            'field',
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
            Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
        )
    ),
    '$tableData' => array(
        array('field' => '<reference><block type="catalog/product_new" /></reference>'),
        array('field' => '<reference></reference>'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => '<reference><block type="Mage_Catalog_Block_Product_New" /></reference>',
                'from'  => array('`field` = ?' => '<reference><block type="catalog/product_new" /></reference>')
            ),
        ),
        'aliases_map' => array(
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK => array(
                'catalog/product_new' => 'Mage_Catalog_Block_Product_New',
            )
        )
    ),
);
