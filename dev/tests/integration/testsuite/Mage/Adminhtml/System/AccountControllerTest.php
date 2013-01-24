<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_System_AccountControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveAction()
    {
        $userId = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getId();
        /** @var $user Mage_User_Model_User */
        $user = Mage::getModel('Mage_User_Model_User')->load($userId);
        $oldPassword = $user->getPassword();

        $password = uniqid('123q');
        $request = $this->getRequest();
        $request->setParam('username', $user->getUsername())->setParam('email', $user->getEmail())
            ->setParam('firstname', $user->getFirstname())->setParam('lastname', $user->getLastname())
            ->setParam('password', $password)->setParam('password_confirmation', $password);
        $this->dispatch('backend/admin/system_account/save');

        /** @var $user Mage_User_Model_User */
        $user = Mage::getModel('Mage_User_Model_User')->load($userId);
        $this->assertNotEquals($oldPassword, $user->getPassword());
        $this->assertTrue(Mage::helper('Mage_Core_Helper_Data')->validateHash($password, $user->getPassword()));
    }
}
