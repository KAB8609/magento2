<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Mage_SalesRule_Model_Rule $salesRule */
$salesRule = Mage::getModel('Mage_SalesRule_Model_Rule');
$salesRule->setData(array(
    'name' => '40% Off on Large Orders',
    'is_active' => 1,
    'customer_group_ids' => array(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID),
    'coupon_type' => Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON,
    'conditions' => array(
        array(
            'type' => 'Mage_SalesRule_Model_Rule_Condition_Address',
            'attribute' => 'base_subtotal',
            'operator' => '>',
            'value' => 800,
        ),
    ),
    'simple_action' => 'by_percent',
    'discount_amount' => 40,
    'stop_rules_processing' => 1,
    'website_ids' => array(Mage::app()->getWebsite()->getId()),
));
$salesRule->save();
