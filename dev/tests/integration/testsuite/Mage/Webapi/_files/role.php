<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var Mage_Webapi_Model_Acl_Role $role */
$role = Mage::getModel('Mage_Webapi_Model_Acl_Role');
$role->setRoleName('test_role')->save();
