<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Acl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ACL tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Acl_SystemPermissionTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Precondition method</p>
     *
     * @test
     * @return array
     */
    public function createAdminWithTestRole()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'System/Permissions'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');

        return $testData;
    }

    /**
     * <p> Ability to edit own role for user with Permission System-Permissions </p>
     * <p>Preconditions</p>
     * <p>1. Role "Role1" with Role resource System-Permissions is created.</p>
     * <p>2. Admin user "User1" with "Role1" is created.</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend using newly created "User1" credentials.</p>
     * <p>2. Navigate to System-Configuration-Permissions-Role.</p>
     * <p>3. Edit "Role1". Add for Role1 access for "System-Configuration" recourse.</p>
     * <p>4. Navigate to System-Configuration.</p>
     * <p>5. Click to all tab(one after the other) in configuration menu.(On the left column).</p>
     * <p>Expected result:</p>
     * <p>All configuration pages(one after other) are successfully opened.</p>
     *
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @TestlinkId TL-MAGE-6071
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function systemPermissions($testData)
    {
        $this->markTestIncomplete('MAGETWO-8428:Exception on page if admin user edit his own role and fatal
                                   error on login page');
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testData);
        // Verify that navigation menu has only 2 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        $this->assertEquals(5, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of child Navigation Menu not equal 5, should be equal 5');
        $this->navigate('manage_roles');
        $editedRole = $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name',
            array('resource_1' => 'System/Configuration'), array('roleName' => $testData['role_name'],
                  'newRoleName' => $testData['role_name']));
        //Data
        $this->adminUserHelper()->editRole($editedRole);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('system_configuration');
        $this->assertEquals(6, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of child Navigation Menu not equal 6, should be equal 6');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_default');
        //verify that this tab equal to resource from ACL tree
        foreach ($tabElement as $tab => $tabName) {
            $this->systemConfigurationHelper()->openConfigurationTab($tab);
        }
    }

    /**
     * <p> Actions available for user with Permission System-Permissions </p>
     *
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @TestlinkId TL-MAGE-6072
     */
    public function systemPermissionsActions($testData)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
                                         array('resource_1' => 'System/Configuration'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testDataForNewUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testDataForNewUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->navigate('manage_roles');
        $dataForDelete = $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name', null,
            array('roleName' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->deleteRole($dataForDelete);
        $this->assertMessagePresent('success', 'success_deleted_role');
        $this->navigate('manage_roles');
        $dataForDeleteOwnRole = $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name', null,
            array('roleName' => $testData['role_name']));
        $this->adminUserHelper()->deleteRole($dataForDeleteOwnRole);
        $this->assertMessagePresent('error', 'delete_self_assigned_role');
    }
}
