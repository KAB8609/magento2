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

/** @var Mage_SalesRule_Model_Rule $salesRule */
$salesRule = Mage::getModel('Mage_SalesRule_Model_Rule');

$data = array(
    'name' => 'Test Coupon',
    'is_active' => true,
    'website_ids' => array(Mage::app()->getStore()->getWebsiteId()),
    'customer_group_ids' => array(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID),
    'coupon_type' => Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC,
    'coupon_code' => uniqid(),
    'simple_action' => Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION,
    'discount_amount' => 10,
    'discount_step' => 1,
);

$salesRule->loadPost($data)->setUseAutoGeneration(false)->save();
