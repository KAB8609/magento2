<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Webapi_Model_Resource_Acl_Role
 */
class Mage_Webapi_Model_Resource_Acl_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for Mage_Webapi_Model_Resource_Acl_Role::getRolesIds()
     *
     * @magentoDataFixture Mage/Webapi/_files/role.php
     * @magentoDataFixture Mage/Webapi/_files/role_with_rule.php
     */
    public function testGetRolesIds()
    {
        $expectedRoleNames = array('test_role', 'Test role');
        /** @var $roleResource Mage_Webapi_Model_Resource_Acl_Role */
        $roleResource = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Role');
        $rolesIds = $roleResource->getRolesIds();
        $this->assertCount(2, $rolesIds);
        foreach ($rolesIds as $roleId) {
            /** @var $role Mage_Webapi_Model_Acl_Role */
            $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')->load($roleId);
            $this->assertNotEmpty($role->getId());
            $this->assertContains($role->getRoleName(), $expectedRoleNames);
        }
    }

    /**
     * Test for Mage_Webapi_Model_Resource_Acl_Role::getRolesList()
     *
     * @magentoDataFixture Mage/Webapi/_files/role.php
     * @magentoDataFixture Mage/Webapi/_files/role_with_rule.php
     */
    public function testGetRolesList()
    {
        /** @var $roleResource Mage_Webapi_Model_Resource_Acl_Role */
        $roleResource = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Role');
        $rolesList = $roleResource->getRolesList();
        $this->assertCount(2, $rolesList);
        foreach ($rolesList as $roleId => $roleName) {
            $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')->load($roleId);
            $this->assertEquals($roleId, $role->getId());
            $this->assertEquals($roleName, $role->getRoleName());
        }
    }

    /**
     * Test for Mage_Webapi_Model_Resource_Acl_Role::_initUniqueFields()
     *
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Role Name already exists.
     * @magentoDataFixture Mage/Webapi/_files/role.php
     */
    public function testInitUniqueFields()
    {
        /** @var $roleResource Mage_Webapi_Model_Resource_Acl_Role */
        $roleResource = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Role');
        $uniqueFields = $roleResource->getUniqueFields();
        $expectedUnique = array(
            array(
                'field' => 'role_name',
                'title' => 'Role Name'
            ),
        );
        $this->assertEquals($expectedUnique, $uniqueFields);

        Mage::getModel('Mage_Webapi_Model_Acl_Role')
            ->setRoleName('test_role')
            ->save();
    }

    /**
     * Test for Mage_Webapi_Model_Resource_Acl_Role::delete()
     *
     * @magentoDataFixture Mage/Webapi/_files/user_with_role.php
     */
    public function testDeleteRole()
    {
        Mage::getModel('Mage_Webapi_Model_Acl_Role')
            ->load('Test role', 'role_name')
            ->delete();
        /** @var Mage_Webapi_Model_Acl_User $user */
        $user = Mage::getModel('Mage_Webapi_Model_Acl_User')
            ->load('test_username','user_name');
        $this->assertNotEmpty($user->getId());
    }
}
