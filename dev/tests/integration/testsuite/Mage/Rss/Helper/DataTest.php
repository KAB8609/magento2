<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Rss
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Rss
 */
class Mage_Rss_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Rss_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = new Mage_Rss_Helper_Data;
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testAuthAdminLoggedIn()
    {
        $admin = new Varien_Object(array('id' => 1));
        $session = Mage::getSingleton('Mage_Rss_Model_Session');
        $session->setAdmin($admin);
        $this->assertEquals($admin, $this->_helper->authAdmin(''));
    }

    public function testAuthAdminNotLogged()
    {
        $this->markTestIncomplete('Incomplete until helper stops exiting script for non-logged user');
        $this->assertFalse($this->_helper->authAdmin(''));
    }

    /**
     * @magentoDataFixture adminUserFixture
     * @magentoAppIsolation enabled
     */
    public function testAuthAdminLogin()
    {
        $_SERVER['PHP_AUTH_USER'] = 'user';
        $_SERVER['PHP_AUTH_PW'] = 'password';
        $this->assertInstanceOf('Mage_Admin_Model_User', $this->_helper->authAdmin(''));

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertFalse(($code >= 300) && ($code < 400));
    }

    public static function adminUserFixture()
    {
        Mage_Admin_Utility_User::getInstance()
            ->createAdmin();
    }

    public static function adminUserFixtureRollback()
    {
        Mage_Admin_Utility_User::getInstance()
            ->destroyAdmin();
    }
}
