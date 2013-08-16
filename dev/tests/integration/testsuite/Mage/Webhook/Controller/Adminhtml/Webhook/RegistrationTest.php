<?php
/**
 * Mage_Webhook_Controller_Adminhtml_Webhook_Registration
 *
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
class Mage_Webhook_Controller_Adminhtml_Webhook_RegistrationTest extends Mage_Backend_Utility_Controller
{
    /** @var  Mage_Webhook_Model_Subscription */
    private $_subscription;
    
    public function setUp()
    {
        parent::setUp();
        $this->_createDummySubscription();
    }

    public function tearDown()
    {
        parent::tearDown();
        if (isset($this->_subscription)) {
            $this->_subscription->delete();
        }
    }

    public function testActivateAction()
    {
        $subscriptionId = $this->_subscription->getId();
        $this->getRequest()->setParam('id', $subscriptionId);
        $this->dispatch('backend/admin/webhook_registration/activate');
        $response = $this->getResponse()->getBody();
        $this->assertContains('page-popup adminhtml-webhook-registration-activate', $response);
        $expectedContent = 'webhook_registration/accept/id/' . $subscriptionId;
        $this->assertContains($expectedContent, $response);
    }

    public function testAcceptAction()
    {
        $subscriptionId = $this->_subscription->getId();
        $this->getRequest()->setParam('id', $subscriptionId);
        $this->dispatch('backend/admin/webhook_registration/accept');
        $this->assertRedirect($this->stringContains('webhook_registration/user/id/' . $subscriptionId));
    }

    public function testUserAction()
    {
        $subscriptionId = $this->_subscription->getId();
        $this->getRequest()->setParam('id', $subscriptionId);
        $this->dispatch('backend/admin/webhook_registration/user');
        $response = $this->getResponse()->getBody();
        $this->assertContains('page-popup adminhtml-webhook-registration-user', $response);
        $expectedContent = 'webhook_registration/register/id/' . $subscriptionId;
        $this->assertContains($expectedContent, $response);
    }

    /**
     * @param array $requestParam
     * @dataProvider requestParamDataProvider
     */
    public function testRegisterActionMissingRequiredFields($requestParam)
    {
        foreach ($requestParam as $key => $value) {
            $this->getRequest()->setParam($key, $value);
        }
        $subscriptionId = $this->_subscription->getId();
        $this->getRequest()->setParam('id', $subscriptionId);
        $this->dispatch('backend/admin/webhook_registration/register');
        $this->assertSessionMessages(
            $this->equalTo(array("API Key, API Secret and Contact Email are required fields.")),
            Mage_Core_Model_Message::ERROR
        );
        $this->assertRedirect($this->stringContains('webhook_registration/failed'));
    }

    /**
     * Data provider for testRegisterActionMissingRequiredFields.
     *
     * @return array
     */
    public function requestParamDataProvider()
    {
        return array(
            array(
                array(
                    'apikey' => 'apikey' . uniqid(),
                    'apisecret' => 'apisecret',
                    'company' => 'company',
                ),
            ),
            array(
                array(
                    'apikey' => 'apikey' . uniqid(),
                    'email' => 'email',
                    'company' => 'company',
                ),
            ),
            array(
                array(
                    'email' => 'email',
                    'apisecret' => 'apisecret',
                    'company' => 'company',
                ),
            )
        );
    }

    /**
     * @param array $requestParam
     * @dataProvider requestParamDataProvider2
     */
    public function testRegisterActionWithRequiredFields($requestParam)
    {
        foreach ($requestParam as $key => $value) {
            $this->getRequest()->setParam($key, $value);
        }

        $subscriptionId = $this->_subscription->getId();
        $this->getRequest()->setParam('id', $subscriptionId);
        $this->dispatch('backend/admin/webhook_registration/register');
        $this->assertSessionMessages(
            $this->equalTo(array("The subscription 'dummy' has been activated.")),
            Mage_Core_Model_Message::SUCCESS
        );
        $this->assertRedirect($this->stringContains('webhook_registration/succeeded'));
    }

    /**
     * Data provider for testRegisterActionWithRequiredFields.
     *
     * @return array
     */
    public function requestParamDataProvider2()
    {
        return array(
            array(
                array(
                    'apikey' => 'apikey' . uniqid(),
                    'apisecret' => 'apisecret',
                    'email' => 'email@domain.com',
                    'company' => 'company',
                ),
            ),
            array(
                array(
                    'apikey' => 'apikey' . uniqid(),
                    'apisecret' => 'apisecret',
                    'email' => 'email@domain.com',
                ),
            ),
        );
    }

    public function testRegisterActionWithInvalidEmailAndFailedAction()
    {
        $requestParam = array(
            'apikey' => 'apikey' . uniqid(),
            'apisecret' => 'apisecret',
            'email' => 'email',
            'company' => 'company',
        );
        foreach ($requestParam as $key => $value) {
            $this->getRequest()->setParam($key, $value);
        }
        $subscriptionId = $this->_subscription->getId();
        $this->getRequest()->setParam('id', $subscriptionId);
        $this->dispatch('backend/admin/webhook_registration/register');
        $this->assertSessionMessages(
            $this->equalTo(array("Invalid Email address provided")), Mage_Core_Model_Message::ERROR
        );
    }

    public function testSucceededAction()
    {
        $this->getRequest()->setParam('id', $this->_subscription->getId());
        $this->dispatch('backend/admin/webhook_registration/succeeded');
        $response = $this->getResponse()->getBody();
        $this->assertContains('page-popup adminhtml-webhook-registration-succeeded', $response);
        $this->assertSessionMessages(
            $this->equalTo(array("The subscription 'dummy' has been activated.")),
            Mage_Core_Model_Message::SUCCESS
        );
    }

    /**
     * Creates a dummy subscription for use in dispatched methods under testing
     */
    private function _createDummySubscription()
    {
        /** @var $factory Mage_Webhook_Model_Subscription_Factory */
        $factory = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Mage_Webhook_Model_Subscription_Factory');
        $this->_subscription = $factory->create()
            ->setName('dummy')
            ->save();
    }
}
