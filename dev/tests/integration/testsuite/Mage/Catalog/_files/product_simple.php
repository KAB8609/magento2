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

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Simple Product')
    ->setSku('simple')
    ->setPrice(10)
    ->setTierPrice(
        array(
            array(
                'website_id' => 0,
                'cust_group' => Mage_Customer_Model_Group::CUST_GROUP_ALL,
                'price_qty'  => 2,
                'price'      => 8,
            ),
            array(
                'website_id' => 0,
                'cust_group' => Mage_Customer_Model_Group::CUST_GROUP_ALL,
                'price_qty'  => 5,
                'price'      => 5,
            ),
        )
    )
    ->setDescription('Description with <b>html tag</b>')

    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')

    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)

    ->setCategoryIds(array(2))

    ->setStockData(
        array(
            'use_config_manage_stock'   => 1,
            'qty'                       => 100,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        )
    )
    ->setCanSaveCustomOptions(true)
    ->setProductOptions(
        array(
            array(
                'id'        => 1,
                'option_id' => 0,
                'previous_group' => 'text',
                'title'     => 'Test Field',
                'type'      => 'field',
                'is_require'=> 1,
                'sort_order'=> 0,
                'price'     => 1,
                'price_type'=> 'fixed',
                'sku'       => '1-text',
                'max_characters' => 100
            ),
            array(
                'id'        => 2,
                'option_id' => 0,
                'previous_group' => 'date',
                'title'     => 'Test Date and Time',
                'type'      => 'date_time',
                'is_require'=> 1,
                'sort_order'=> 0,
                'price'     => 2,
                'price_type'=> 'fixed',
                'sku'       => '2-date',
            ),
            array(
                'id'        => 3,
                'option_id' => 0,
                'previous_group' => 'select',
                'title'     => 'Test Select',
                'type'      => 'drop_down',
                'is_require'=> 1,
                'sort_order'=> 0,
                'values'    => array(
                    array(
                        'option_type_id'=> -1,
                        'title'         => 'Option 1',
                        'price'         => 3,
                        'price_type'    => 'fixed',
                        'sku'           => '3-1-select',
                    ),
                    array(
                        'option_type_id'=> -1,
                        'title'         => 'Option 2',
                        'price'         => 3,
                        'price_type'    => 'fixed',
                        'sku'           => '3-2-select',
                    ),
                )
            ),
            array(
                'id'        => 4,
                'option_id' => 0,
                'previous_group' => 'select',
                'title'     => 'Test Radio',
                'type'      => 'radio',
                'is_require'=> 1,
                'sort_order'=> 0,
                'values'    => array(
                    array(
                        'option_type_id'=> -1,
                        'title'         => 'Option 1',
                        'price'         => 3,
                        'price_type'    => 'fixed',
                        'sku'           => '4-1-radio',
                    ),
                    array(
                        'option_type_id'=> -1,
                        'title'         => 'Option 2',
                        'price'         => 3,
                        'price_type'    => 'fixed',
                        'sku'           => '4-2-radio',
                    ),
                )
            ),
        )
    )
    ->setHasOptions(true)
    ->save();
