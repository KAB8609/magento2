<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ACL
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
class Community2_Mage_ACL_CmsWidgetTest extends Mage_Selenium_TestCase
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
     * <p>Preconditions</p>
     * <p>Create Admin User with full CMS widget resources role</p>
     *
     * @return array
     * @test
     */
    public function roleResourceAccessCmsWidget()
    {
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'CMS/Widgets'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        return $loginData;
    }

    /**
     * <p>Admin with Resource: CMS widget has access to CMS/widgets menu. All necessary elements are presented</p>
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>Expected results:</p>
     * <p>1. Current page is Manage Widgets</p>
     * <p>2. Navigation menu has only 1 parent element(CMS)</p>
     * <p>3. Navigation menu(CMS) has only 1 child element(Pages)</p>
     * <p>4. Manage Widgets contains:</p>
     * <p>4.1 Buttons: "Add New Widget Instance", "Reset Filter", "Search"</p>
     * <p>4.2 Fields: "page", "filter_widget_id", "filter_title", "filter_sort_order"</p>
     * <p>4.3 Dropdowns: "view_per_page", "filter_type", "filter_package_theme"</p>
     *
     * @param $loginData
     *
     * @depends roleResourceAccessCmsWidget
     * @test
     * @TestlinkId TL-MAGE-6160
     */
    public function verifyScopeCmsWidgetOneRoleResource($loginData)
    {
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_cms_widgets');
        // Verify that navigation menu has only 1 parent element
        $this->assertEquals('1', count($this->getElementsByXpath(
                $this->_getControlXpath('pageelement', 'navigation_menu_items'))),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals('1', count($this->getElementsByXpath(
                $this->_getControlXpath('pageelement', 'navigation_children_menu_items'))),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify  that necessary elements are present on page
        $elements= $this->loadDataSet('CmsWidgetElements','manage_cms_widget_elements');
        $resultElementsArray = array();
        foreach ($elements as $key => $value) {
            $resultElementsArray = array_merge($resultElementsArray, (array_fill_keys(array_keys($value), $key)));
        }
        foreach ($resultElementsArray as $elementName => $elementType) {
            if (!$this->controlIsVisible($elementType, $elementName)) {
                $this->addVerificationMessage("Element type = '$elementType'
                                                       name = '$elementName' is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Admin with Resource: CMS/Widgets can create new widget with all fielded fields</p>
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Click "Add New Widget Instance" button</p>
     * <p>3. Create widget with all required field.</p>
     * <p>Expected results:</p>
     * <p>1. Widget is created</p>
     * <p>2. Success Message is appeared "The widget has been saved."</p>
     *
     * @param array $loginData
     *
     * @depends roleResourceAccessCmsWidget
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6159
     */
    public function  createNewWidget($loginData)
    {
        //Steps
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_cms_widgets');
        $widgetData = $this->loadDataSet('CmsWidget', 'cms_page_link_widget_req');
        $widgetToDelete = array('filter_type' => $widgetData['settings']['type'],
                                'filter_title' => $widgetData['frontend_properties']['widget_instance_title']);
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widgetData);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
        return $widgetToDelete;
    }

    /**
     * <p>Admin with Resource: CMS/Widgets can edit cms widget and save using "Save and Continue Edit" button</p>
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Find newly created test cms widget in grid and click</p>
     * <p>3. Create widget with all required field</p>
     * <p>4. Click "Save and Continue Edit" button</p>
     * <p>Expected results:</p>
     * <p>1. Widget is saved</p>
     * <p>2. Success Message is appeared "The widget has been saved."</p>
     *
     * @param $loginData
     * @param $widgetToDelete
     *
     * @depends roleResourceAccessCmsWidget
     * @depends createNewWidget
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6158
     */
    public function  editWidget($loginData, $widgetToDelete)
    {
        //Steps
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_cms_widgets');
        $this->cmsWidgetsHelper()->openWidget($widgetToDelete);
        $this->fillField('sort_order', '1');
        $this->clickControlAndWaitMessage('button', 'save_and_continue_edit', false);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
    }

    /**
     * <p>Admin with Resource: CMS/Widget can delete cms widget</p>
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Find test cms widget in grid and click</p>
     * <p>3. Click "Delete" button</p>
     * <p>4. Click "OK" button for confirm action</p>
     * <p>Expected results:</p>
     * <p>1. Widget is deleted</p>
     * <p>2. Success Message is appeared "The widget has been deleted."</p>
     *
     * @param $loginData
     * @param $widgetToDelete
     *
     * @depends roleResourceAccessCmsWidget
     * @depends createNewWidget
     *
     * @test
     * @TestlinkId TL-MAGE-6157
     */
    public function deleteNewWidget($loginData, $widgetToDelete)
    {
        //Steps
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_cms_widgets');
        $this->cmsWidgetsHelper()->deleteWidget($widgetToDelete);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_deleted_widget');
    }
}

