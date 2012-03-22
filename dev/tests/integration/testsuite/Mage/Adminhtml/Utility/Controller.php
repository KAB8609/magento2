<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A parent class for adminhtml controllers - contains directives for admin user creation and logging in
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.numberOfChildren)
 */
class Mage_Adminhtml_Utility_Controller extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Admin_Model_Session
     */
    protected $_session;

    protected function setUp()
    {
        parent::setUp();

        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();
        Mage_Admin_Utility_User::getInstance()
            ->createAdmin();

        $this->_session = new Mage_Admin_Model_Session();
        $this->_session->login(Mage_Admin_Utility_User::CRED_USERNAME, Mage_Admin_Utility_User::CRED_PASSWORD);
    }

    protected function tearDown()
    {
        $this->_session->logout();
        Mage_Admin_Utility_User::getInstance()
            ->destroyAdmin();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOnSecretKey();

        parent::tearDown();
    }
}
