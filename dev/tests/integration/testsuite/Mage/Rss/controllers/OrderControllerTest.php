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

class Mage_Rss_OrderControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Reuse URI for "new" action
     */
    const NEW_ORDER_URI = 'rss/order/new';

    public function testNewActionAuthorizationFailed()
    {
        $this->dispatch(self::NEW_ORDER_URI);
        $this->assertHeaderPcre('Http/1.1', '/^401 Unauthorized$/');
    }

    /**
     * magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testNewAction()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + fixture');

        $this->getRequest()->setServer(array(
            'PHP_AUTH_USER' => Magento_Test_Bootstrap::ADMIN_NAME,
            'PHP_AUTH_PW' => Magento_Test_Bootstrap::ADMIN_PASSWORD
        ));
        $this->dispatch(self::NEW_ORDER_URI);
        $this->assertHeaderPcre('Content-Type', '/text\/xml/');
        $this->assertContains('#100000001', $this->getResponse()->getBody());
    }

    public function testNotLoggedIn()
    {
        $this->dispatch(self::NEW_ORDER_URI);
        $this->assertHeaderPcre('Http/1.1', '/^401 Unauthorized$/');
    }

    /**
     * @param string $login
     * @param string $password
     * @dataProvider invalidAccessDataProvider
     * magentoDataFixture Mage/User/_files/dummy_user.php
     * @covers Mage_Rss_OrderController::authenticateAndAuthorizeAdmin
     */
    public function testInvalidAccess($login, $password)
    {
        $this->markTestIncomplete('Need to fix DI dependencies + fixture');

        $this->getRequest()->setServer(array('PHP_AUTH_USER' => $login, 'PHP_AUTH_PW' => $password));
        $this->dispatch(self::NEW_ORDER_URI);
        $this->assertHeaderPcre('Http/1.1', '/^401 Unauthorized$/');
    }

    /**
     * @return array
     */
    public function invalidAccessDataProvider()
    {
        return array(
            'no login' => array('', Magento_Test_Bootstrap::ADMIN_PASSWORD),
            'no password' => array(Magento_Test_Bootstrap::ADMIN_NAME, ''),
            'no login and password' => array('', ''),
            'user with inappropriate ACL' => array('dummy_username', 'dummy_password1'),
        );
    }
}
