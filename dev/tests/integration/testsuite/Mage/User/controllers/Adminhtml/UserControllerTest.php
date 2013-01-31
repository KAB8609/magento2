<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Adminhtml_UserControllerTest extends Mage_Backend_Utility_Controller
{
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/user/index');
        $response = $this->getResponse()->getBody();
        $this->assertContains('Users', $response);
        $this->assertSelectCount('#permissionsUserGrid_table', 1, $response);
    }

    /**
     * @magentoConfigFixture global/functional_limitation/max_admin_user_count 1
     */
    public function testIndexActionLimitedUsers()
    {
        $this->dispatch('backend/admin/user/index');
        $response = $this->getResponse()->getBody();
        $this->assertNotContains('Add New User', $response);
        $this->assertContains(Mage_User_Model_Resource_User::getMessageUserCreationProhibited(), $response);
    }

    public function testSaveActionNoData()
    {
        $this->dispatch('backend/admin/user/save');
        $this->assertRedirect($this->stringContains('backend/admin/user/index/'));
    }

    /**
     * @magentoDataFixture Mage/User/_files/dummy_user.php
     */
    public function testSaveActionWrongId()
    {
        /** @var $user Mage_User_Model_User */
        $user = Mage::getModel('Mage_User_Model_User')->loadByUsername('dummy_username');
        $userId = $user->getId();
        $this->assertNotEmpty($userId, 'Broken fixture');
        $user->delete();
        $this->getRequest()->setPost('user_id', $userId);
        $this->dispatch('backend/admin/user/save');
        $this->assertSessionMessages(
            $this->equalTo(array('This user no longer exists.')), Mage_Core_Model_Message::ERROR
        );
        $this->assertRedirect($this->stringContains('backend/admin/user/index/'));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveAction()
    {
        $this->_createNew();
        $this->assertSessionMessages(
            $this->equalTo(array('The user has been saved.')), Mage_Core_Model_Message::SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/admin/user/index/'));
    }

    /**
     * Create new user through dispatching save action
     */
    private function _createNew()
    {
        $fixture = uniqid();
        $this->getRequest()->setPost(array(
            'username' => $fixture,
            'email' => "{$fixture}@example.com",
            'firstname' => 'First',
            'lastname' => 'Last',
            'password' => 'password_with_1_number',
            'password_confirmation' => 'password_with_1_number',
        ));
        $this->dispatch('backend/admin/user/save');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoConfigFixture global/functional_limitation/max_admin_user_count 1
     */
    public function testSaveActionLimitedUsers()
    {
        $this->_createNew();
        $this->assertSessionMessages(
            $this->equalTo(array('You are using the maximum number of admin accounts allowed.')),
            Mage_Core_Model_Message::ERROR
        );
        $this->assertRedirect($this->stringContains('backend/admin/user/edit/'));
    }

    /**
     * @covers Mage_User_Adminhtml_UserController::rolesGridAction
     */
    public function testRoleGridAction()
    {
        $this->getRequest()
            ->setParam('ajax', true)
            ->setParam('isAjax', true);
        $this->dispatch('backend/admin/user/roleGrid');
        $expected = '%a<table %a id="permissionsUserGrid_table">%a';
        $this->assertStringMatchesFormat($expected, $this->getResponse()->getBody());
    }

    public function testRolesGridAction()
    {
        $this->getRequest()
            ->setParam('ajax', true)
            ->setParam('isAjax', true)
            ->setParam('user_id', 1);
        $this->dispatch('backend/admin/user/rolesGrid');
        $expected = '%a<table %a id="permissionsUserRolesGrid_table">%a';
        $this->assertStringMatchesFormat($expected, $this->getResponse()->getBody());
    }

    public function testEditAction()
    {
        $this->getRequest()->setParam('user_id', 1);
        $this->dispatch('backend/admin/user/edit');
        $response = $this->getResponse()->getBody();
        //check "User Information" header and fieldset
        $this->assertContains('data-ui-id="adminhtml-user-edit-tabs-title"', $response);
        $this->assertContains('User Information', $response);
        $this->assertSelectCount('#user_base_fieldset', 1, $response);
    }
}
