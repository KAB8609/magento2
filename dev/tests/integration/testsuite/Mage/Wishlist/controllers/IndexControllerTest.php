<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Wishlist_IndexControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Verify wishlist view action
     *
     * The following is verified:
     * - Mage_Wishlist_Model_Resource_Item_Collection
     * - Mage_Wishlist_Block_Customer_Wishlist
     * - Mage_Wishlist_Block_Customer_Wishlist_Items
     * - Mage_Wishlist_Block_Customer_Wishlist_Item_Column
     * - Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Cart
     * - Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Comment
     * - Mage_Wishlist_Block_Customer_Wishlist_Button
     * - that Mage_Wishlist_Block_Customer_Wishlist_Item_Options doesn't throw a fatal error
     *
     * @magentoDataFixture Mage/Wishlist/_files/wishlist.php
     */
    public function testItemColumnBlock()
    {
        $session = Mage::getModel('Mage_Customer_Model_Session');
        $session->login('customer@example.com', 'password');
        $this->dispatch('wishlist/index/index');
        $body = $this->getResponse()->getBody();
        $this->assertStringMatchesFormat('%A<img src="%Asmall_image.jpg" %A alt="Simple Product"%A/>%A', $body);
        $this->assertStringMatchesFormat('%A<textarea name="description[%d]"%A', $body);
    }
}
