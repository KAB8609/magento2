<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Utility;

/**
 * A parent class for backend controllers - contains directives for admin user creation and authentication
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.numberOfChildren)
 */
class Controller extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_session;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    protected function setUp()
    {
        parent::setUp();

        $this->_objectManager->get('Magento\Backend\Model\Url')->turnOffSecretKey();

        $this->_auth = $this->_objectManager->get('Magento\Backend\Model\Auth');
        $this->_session = $this->_auth->getAuthStorage();
        $this->_auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
    }

    protected function tearDown()
    {
        /** @var $checkoutSession \Magento\Checkout\Model\Session */
        $checkoutSession = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $checkoutSession->clearStorage();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        if (isset($_COOKIE[$checkoutSession->getName()])) {
            unset($_COOKIE[$checkoutSession->getName()]);
        }

        $this->_auth = null;
        $this->_session = null;
        $this->_objectManager->get('Magento\Backend\Model\Url')->turnOnSecretKey();
        parent::tearDown();
    }

    /**
     * Utilize backend session model by default
     *
     * @param \PHPUnit_Framework_Constraint $constraint
     * @param string|null $messageType
     * @param string $messageManager
     */
    public function assertSessionMessages(
        \PHPUnit_Framework_Constraint $constraint, $messageType = null, $messageManager = 'Magento\Message\Manager'
    ) {
        parent::assertSessionMessages($constraint, $messageType, $messageManager);
    }
}
