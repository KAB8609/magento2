<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId(Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('GiftCard Product')
    ->setSku('gift1')
    ->setPrice(10)
    ->setDescription('Description with <b>html tag</b>')
    ->setMetaTitle('gift meta title')
    ->setMetaKeyword('gift meta keyword')
    ->setMetaDescription('gift meta description')
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setCategoryIds(array(2))
    ->setStockData(
        array(
            'use_config_manage_stock'   => 0,
        )
    )
    ->setCanSaveCustomOptions(true)
    ->setHasOptions(true)
    ->setAllowOpenAmount(1)
    ->save();

/** @var $product Mage_Catalog_Model_Product */
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->load(1);

$requestInfo = new Varien_Object(array(
    'qty' => 1,
    'giftcard_amount'         => 'custom',
    'custom_giftcard_amount'  => 200,
    'giftcard_sender_name'    => 'Sender',
    'giftcard_sender_email'   => 'aerfg@sergserg.com',
    'giftcard_recipient_name' => 'Recipient',
    'giftcard_recipient_email'=> 'awefaef@dsrthb.com',
    'giftcard_message'        => 'message'
));

require __DIR__ . '/../../../Mage/Checkout/_files/cart.php';
