<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../Checkout/_files/simple_product.php';
/** @var $product Mage_Catalog_Model_Product */
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->load(1);

/** @var $product Mage_Catalog_Model_Product */
$product->setCanSaveCustomOptions(true)
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
            )
        )
    )
    ->setHasOptions(true)
    ->save();

/** @var $product Mage_Catalog_Model_Product */
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->load(1);
$optionId = key($product->getOptions());

$requestInfo = new Varien_Object(array(
    'qty' => 1,
    'options' => array(
        $optionId => 'test'
    )
));

require __DIR__ . '/../../Checkout/_files/cart.php';
