<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$taxClasses = Mage::getResourceModel('tax/class_collection')->toArray();
$taxClass = reset($taxClasses['items']);

return array(
    'name' => 'Test_new',
    'description' => 'Test description_new',
    'short_description' => 'Test short description_new',
    'news_from_date' => '02/16/2013',
    'news_to_date' => '16.02.2013',
    'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
    'url_key' => 'test-new',
    'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
    'is_returnable' => 0,
    'price' => 15.50,
    'special_price' => 15.2,
    'special_from_date' => '02/16/2013',
    'special_to_date' => '03/17/2013',
    'msrp_enabled' => 0,
    'msrp_display_actual_price_type' => 0,
    'msrp' => 15.01,
    'tax_class_id' => $taxClass['class_id'],
    'meta_title' => 'Test title_new',
    'meta_keyword' => 'Test keyword_new',
    'meta_description' => 'Test description_new',
    'custom_design' => 'base/default',
    'custom_design_from' => '02/16/2013',
    'custom_design_to' => '05/01/2013',
    'custom_layout_update' => '<xml><layout>Test Custom Layout Update_new</layout></xml>',
    'page_layout' => 'empty',
    'options_container' => 'container2',
    'gift_wrapping_available' => 0,
    'gift_wrapping_price' => 15.56,
    'stock_data' => array(
        'manage_stock' => 1,
        'qty' => 10,
        'min_qty' => 10.56,
        'min_sale_qty' => 10,
        'max_sale_qty' => 10,
        'is_qty_decimal' => 0,
        'backorders' => 1,
        'notify_stock_qty' => -500.99,
        'enable_qty_increments' => 0,
        'is_in_stock' => 1
    )
);
