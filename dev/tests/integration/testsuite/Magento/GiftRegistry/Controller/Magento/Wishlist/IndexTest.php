<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Controller_Magento_Wishlist_IndexTest
    extends Magento_TestFramework_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIndexAction()
    {
        $this->markTestIncomplete('Bug MAGE-6447');
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        $session = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Customer_Model_Session', array($logger));
        $this->assertTrue($session->login('customer@example.com', 'password')); // fixture
        $this->dispatch('wishlist/index/index');
        $this->assertContains('id="giftregistry-form">', $this->getResponse()->getBody());
    }
}
