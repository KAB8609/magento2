<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_GiftRegistry
 * @group module:Mage_Wishlist
 */
class Enterprise_GiftRegistry_Mage_Wishlist_IndexControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testIndexAction()
    {
        $session = new Mage_Customer_Model_Session;
        $this->assertTrue($session->login('customer@example.com', 'password')); // fixture
        $this->dispatch('wishlist/index/index');
        $this->assertContains('id="giftregistry-form">', $this->getResponse()->getBody());
    }
}
