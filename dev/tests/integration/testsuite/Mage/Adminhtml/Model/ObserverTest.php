<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Model_Observer
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Adminhtml_Model_Observer();
    }

    public function testActionPreDispatchAdminNotLogged()
    {
        $request = Mage::app()->getRequest();
        $this->assertEmpty($request->getRouteName());
        $this->assertEmpty($request->getControllerName());
        $this->assertEmpty($request->getActionName());

        $observer = $this->_buildObserver();
        $this->_model->actionPreDispatchAdmin($observer);

        $this->assertEquals('adminhtml', $request->getRouteName());
        $this->assertEquals('index', $request->getControllerName());
        $this->assertEquals('login', $request->getActionName());
    }

    /**
     * @magentoDataFixture adminUserFixture
     * @magentoAppIsolation enabled
     */
    public function testActionPreDispatchAdminLoggedRedirect()
    {
        $observer = $this->_buildObserver();
        $this->_model->actionPreDispatchAdmin($observer);

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertTrue($code >= 300 && $code < 400);

        $session = Mage::getSingleton('Mage_Admin_Model_Session');
        $this->assertTrue($session->isLoggedIn());
    }

    /**
     * @magentoDataFixture adminUserFixture
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store admin/security/use_form_key 0
     */
    public function testActionPreDispatchAdminLoggedNoRedirect()
    {
        $observer = $this->_buildObserver();
        $this->_model->actionPreDispatchAdmin($observer);

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertFalse($code >= 300 && $code < 400);

        $session = Mage::getSingleton('Mage_Admin_Model_Session');
        $this->assertTrue($session->isLoggedIn());
    }

    /**
     * Builds a dummy observer for testing adminPreDispath method
     *
     * @return Varien_Object
     */
    protected function _buildObserver()
    {
        $request = Mage::app()->getRequest();
        $request->setPost('login', array('username' => 'user', 'password' => 'password'));

        $controller = new Varien_Object(array('request' => $request));
        $event = new Varien_Object(array('controller_action' => $controller));
        $observer = new Varien_Object(array('event' => $event));
        return $observer;
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
