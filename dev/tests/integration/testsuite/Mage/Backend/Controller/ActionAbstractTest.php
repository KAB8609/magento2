<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Controller_ActionAbstract.
 */
class Mage_Backend_Controller_ActionAbstractTest extends Mage_Backend_Utility_Controller
{
    /**
     * Check redirection to startup page for logged user
     * @magentoConfigFixture global/areas/adminhtml/frontName backend
     * @magentoConfigFixture current_store admin/security/use_form_key 1
     */
    public function testPreDispatchWithEmptyUrlRedirectsToStartupPage()
    {
        $expected = Mage::getSingleton('Mage_Backend_Model_Url')->getUrl('adminhtml/dashboard');
        $this->dispatch('backend');
        $this->assertRedirect($this->stringStartsWith($expected));
    }

    /**
     * Check login redirection
     *
     * @covers Mage_Backend_Controller_ActionAbstract::_initAuthentication
     * @magentoDbIsolation enabled
     */
    public function testInitAuthentication()
    {
        /**
         * Logout current session
         */
        $this->_auth->logout();

        $postLogin = array('login' => array(
            'username' => Magento_Test_Bootstrap::ADMIN_NAME,
            'password' => Magento_Test_Bootstrap::ADMIN_PASSWORD
        ));

        $url = Mage::getSingleton('Mage_Backend_Model_Url')->getUrl('adminhtml/system_account/index');
        $this->getRequest()->setPost($postLogin);
        $this->dispatch($url);

        $expected = 'backend/admin/system_account/index';
        $this->assertRedirect($this->stringContains($expected));
    }

    /**
     * Check layout attribute "acl" for check access to
     *
     * @param string $blockName
     * @param string $resource
     * @param bool $isLimitedAccess
     * @dataProvider nodesWithAcl
     */
    public function testAclInNodes($blockName, $resource, $isLimitedAccess)
    {
        /** @var $noticeInbox Mage_AdminNotification_Model_Inbox */
        $noticeInbox = Mage::getModel('Mage_AdminNotification_Model_Inbox');
        if (!$noticeInbox->loadLatestNotice()->getId()) {
            $noticeInbox->addCritical('Test notice', 'Test description');
        }

        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);

        /** @var $acl Magento_Acl */
        $acl = Mage::getSingleton('Mage_Core_Model_Acl_Builder')->getAcl(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        if ($isLimitedAccess) {
            $acl->deny(null, $resource);
        }

        $this->dispatch('backend/admin/dashboard');

        $layout = Mage::app()->getLayout();
        $actualBlocks = $layout->getAllBlocks();

        $this->assertNotEmpty($actualBlocks);
        if ($isLimitedAccess) {
            $this->assertNotContains($blockName, array_keys($actualBlocks));
        } else {
            $this->assertContains($blockName, array_keys($actualBlocks));
        }
    }

    /**
     * Data provider with expected blocks with acl properties
     *
     * @return array
     */
    public function nodesWithAcl()
    {
        return array(
            array('notification_toolbar', 'Mage_AdminNotification::show_toolbar', true),
            array('notification_window', 'Mage_AdminNotification::show_toolbar', true),
            array('notification_toolbar', 'Mage_AdminNotification::show_toolbar', false),
            array('notification_window', 'Mage_AdminNotification::show_toolbar', false),
        );
    }
}
