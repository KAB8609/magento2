<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Save administrators group role and rules
 */

/** @var $roleCollection Mage_User_Model_Resource_Role_Collection */
$roleCollection = $admGroupRole->getCollection()
    ->addFieldToFilter('parent_id', 0)
    ->addFieldToFilter('tree_level', 1)
    ->addFieldToFilter('role_type', 'G')
    ->addFieldToFilter('user_id', 0)
    ->addFieldToFilter('role_name', 'Administrators');

if ($roleCollection->count() == 0) {
    $admGroupRole = Mage::getModel('Mage_User_Model_Role')->setData(array(
        'parent_id'     => 0,
        'tree_level'    => 1,
        'sort_order'    => 1,
        'role_type'     => 'G',
        'user_id'       => 0,
        'role_name'     => 'Administrators'
    ))
    ->save();
} else {
    foreach ($roleCollection as $item) {
        $admGroupRole = $item;
        break;
    }
}

/** @var $rulesCollection Mage_User_Model_Resource_Rules_Collection */
$rulesCollection = Mage::getModel('Mage_User_Model_Rules')->getCollection()
    ->addFieldToFilter('role_id', $admGroupRole->getId())
    ->addFieldToFilter('resource_id', 'all')
    ->addFieldToFilter('role_type', 'G')
    ->loadData();
if ($rulesCollection->count() == 0) {
    Mage::getModel('Mage_User_Model_Rules')->setData(array(
        'role_id'       => $admGroupRole->getId(),
        'resource_id'   => 'Mage_Adminhtml::all',
        'privileges'    => null,
        'role_type'     => 'G',
        'permission'    => 'allow'
        ))
    ->save();
} else {
    foreach ($rulesCollection as $rule) {
        $rule->setData('resource_id', 'Mage_Adminhtml::all')
            ->save();
    }
}
