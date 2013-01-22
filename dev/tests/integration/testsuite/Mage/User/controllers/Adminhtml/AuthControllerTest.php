<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_User_Adminhtml_AuthController.
 */
class Mage_User_Adminhtml_AuthControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * Test form existence
     * @covers Mage_User_Adminhtml_AuthController::forgotpasswordAction
     */
    public function testFormForgotpasswordAction()
    {
        $this->dispatch('backend/admin/auth/forgotpassword');
        $expected = 'Forgot your user name or password?';
        $this->assertContains($expected, $this->getResponse()->getBody());
    }

    /**
     * Test redirection to startup page after success password recovering posting
     *
     * @covers Mage_User_Adminhtml_AuthController::forgotpasswordAction
     */
    public function testForgotpasswordAction()
    {
        $this->getRequest()->setPost('email', 'test@test.com');
        $this->dispatch('backend/admin/auth/forgotpassword');
        $this->assertRedirect($this->equalTo(Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl()));
    }

    /**
     * Test reset password action
     *
     * @covers Mage_User_Adminhtml_AuthController::resetPasswordAction
     * @covers Mage_User_Adminhtml_AuthController::_validateResetPasswordLinkToken
     * @magentoDataFixture Mage/User/_files/dummy_user.php
     */
    public function testResetPasswordAction()
    {
        $user = Mage::getModel('Mage_User_Model_User')->loadByUsername('dummy_username');
        $resetPasswordToken = null;
        if ($user->getId()) {
            $resetPasswordToken = Mage::helper('Mage_User_Helper_Data')
                ->generateResetPasswordLinkToken();
            $user->changeResetPasswordLinkToken($resetPasswordToken);
            $user->save();
        }

        $this->getRequest()
            ->setQuery('token', $resetPasswordToken)
            ->setQuery('id', $user->getId());
        $this->dispatch('backend/admin/auth/resetpassword');

        $this->assertEquals('adminhtml', $this->getRequest()->getRouteName());
        $this->assertEquals('auth', $this->getRequest()->getControllerName());
        $this->assertEquals('resetpassword', $this->getRequest()->getActionName());

        $this->assertContains($resetPasswordToken, $this->getResponse()->getBody());
    }

    /**
     * @covers Mage_User_Adminhtml_AuthController::resetPasswordAction
     * @covers Mage_User_Adminhtml_AuthController::_validateResetPasswordLinkToken
     */
    public function testResetPasswordActionWithDummyToken()
    {
        $this->getRequest()->setQuery('token', 'dummy')->setQuery('id', 1);
        $this->dispatch('backend/admin/auth/resetpassword');
        $this->assertSessionMessages(
            $this->equalTo(array('Your password reset link has expired.')), Mage_Core_Model_Message::ERROR
        );
        $this->assertRedirect();
    }

    /**
     * @covers Mage_User_Adminhtml_AuthController::resetPasswordPostAction
     * @covers Mage_User_Adminhtml_AuthController::_validateResetPasswordLinkToken
     * @magentoDataFixture Mage/User/_files/dummy_user.php
     */
    public function testResetPasswordPostAction()
    {
        $user = Mage::getModel('Mage_User_Model_User')->loadByUsername('dummy_username');
        $resetPasswordToken = null;
        if ($user->getId()) {
            $resetPasswordToken = Mage::helper('Mage_User_Helper_Data')
                ->generateResetPasswordLinkToken();
            $user->changeResetPasswordLinkToken($resetPasswordToken);
            $user->save();
        }

        $newDummyPassword = 'new_dummy_password2';

        $this->getRequest()
            ->setQuery('token', $resetPasswordToken)
            ->setQuery('id', $user->getId())
            ->setPost('password', $newDummyPassword)
            ->setPost('confirmation', $newDummyPassword);

        $this->dispatch('backend/admin/auth/resetpasswordpost');

        $this->assertRedirect($this->equalTo(Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl()));

        $user = Mage::getModel('Mage_User_Model_User')
            ->loadByUsername('dummy_username');

        $this->assertTrue(Mage::helper('Mage_Core_Helper_Data')->validateHash($newDummyPassword, $user->getPassword()));
    }

    /**
     * @covers Mage_User_Adminhtml_AuthController::resetPasswordPostAction
     * @covers Mage_User_Adminhtml_AuthController::_validateResetPasswordLinkToken
     * @magentoDataFixture Mage/User/_files/dummy_user.php
     */
    public function testResetPasswordPostActionWithDummyToken()
    {
        $this->getRequest()->setQuery('token', 'dummy')->setQuery('id', 1);
        $this->dispatch('backend/admin/auth/resetpasswordpost');
        $this->assertSessionMessages(
            $this->equalTo(array('Your password reset link has expired.')), Mage_Core_Model_Message::ERROR
        );
        $this->assertRedirect($this->equalTo(Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl()));
    }

    /**
     * @covers Mage_User_Adminhtml_AuthController::resetPasswordPostAction
     * @covers Mage_User_Adminhtml_AuthController::_validateResetPasswordLinkToken
     * @magentoDataFixture Mage/User/_files/dummy_user.php
     */
    public function testResetPasswordPostActionWithInvalidPassword()
    {
        $user = Mage::getModel('Mage_User_Model_User')->loadByUsername('dummy_username');
        $resetPasswordToken = null;
        if ($user->getId()) {
            $resetPasswordToken = Mage::helper('Mage_User_Helper_Data')
                ->generateResetPasswordLinkToken();
            $user->changeResetPasswordLinkToken($resetPasswordToken);
            $user->save();
        }

        $newDummyPassword = 'new_dummy_password2';

        $this->getRequest()
            ->setQuery('token', $resetPasswordToken)
            ->setQuery('id', $user->getId())
            ->setPost('password', $newDummyPassword)
            ->setPost('confirmation', 'invalid');

        $this->dispatch('backend/admin/auth/resetpasswordpost');

        $this->assertRedirect();
    }
}
