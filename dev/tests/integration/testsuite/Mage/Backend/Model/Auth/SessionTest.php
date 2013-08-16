<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Backend_Model_Auth_SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Auth
     */
    protected $_auth;

    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_model;

    public function setUp()
    {
        parent::setUp();
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Mage_Core_Model_Config_Scope')
            ->setCurrentScope(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        $this->_auth  = Mage::getModel('Mage_Backend_Model_Auth');
        $this->_model = Mage::getModel('Mage_Backend_Model_Auth_Session');
        $this->_auth->setAuthStorage($this->_model);
    }

    protected function tearDown()
    {
        $this->_model = null;
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Mage_Core_Model_Config_Scope')
            ->setCurrentScope(null);
    }

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/session_lifetime 100
     */
    public function testIsLoggedIn()
    {
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
        $this->assertTrue($this->_model->isLoggedIn());

        $this->_model->setUpdatedAt(time() - 101);
        $this->assertFalse($this->_model->isLoggedIn());
    }

    /**
     * Disabled form security in order to prevent exit from the app
     * @magentoConfigFixture current_store admin/security/session_lifetime 59
     */
    public function testIsLoggedInWithIgnoredLifetime()
    {
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
        $this->assertTrue($this->_model->isLoggedIn());

        $this->_model->setUpdatedAt(time() - 101);
        $this->assertTrue($this->_model->isLoggedIn());
    }
}
