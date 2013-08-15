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
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Controller_Sales_OrderTest extends Mage_Backend_Utility_Controller
{
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/sales_order/index');
        $this->assertContains('Total 0 records found', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testIndexActionWithOrder()
    {
        $this->dispatch('backend/admin/sales_order/index');
        $this->assertContains('Total 1 records found', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testOrderViewAction()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('Mage_Sales_Model_Order');
        $order->load('100000001', 'increment_id');
        $this->dispatch('backend/admin/sales_order/view/order_id/' . $order->getId());
        $this->assertContains('Los Angeles', $this->getResponse()->getBody());
    }

    public function testAddressActionNonExistingAddress()
    {
        $this->getRequest()->setParam('address_id', -1);
        $this->dispatch('backend/admin/sales_order/address');
        $this->assertRedirect();
    }

    /**
     * @magentoDataFixture Mage/Adminhtml/controllers/Sales/_files/address.php
     */
    public function testAddressActionNoVAT()
    {
        /** @var $address Mage_Sales_Model_Order_Address */
        $address = Mage::getModel('Mage_Sales_Model_Order_Address');
        $address->load('a_unique_firstname', 'firstname');
        $this->getRequest()->setParam('address_id', $address->getId());
        $this->dispatch('backend/admin/sales_order/address');
        $html = $this->getResponse()->getBody();
        $prohibitedStrings = array('validate-vat', 'validateVat', 'Validate VAT');
        foreach ($prohibitedStrings as $string) {
            $this->assertNotContains($string, $html, 'VAT button must not be shown while editing address', true);
        }
    }

    /**
     * Test add comment to order
     *
     * @param $status
     * @param $comment
     * @param $response
     * @magentoDataFixture Mage/Sales/_files/order.php
     * @dataProvider getAddCommentData
     */
    public function testAddCommentAction($status, $comment, $response)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('Mage_Sales_Model_Order');
        $order->load('100000001', 'increment_id');

        $this->getRequest()->setPost(array('history' => array('status' => $status, 'comment' => $comment)));
        $this->dispatch('backend/admin/sales_order/addComment/order_id/' . $order->getId());
        $html = $this->getResponse()->getBody();

        $this->assertContains($response, $html);
    }

    /**
     * Get Add Comment Data
     *
     * @return array
     */
    public function getAddCommentData()
    {
        return array(
            array('status' => 'pending', 'comment' => 'Test comment', 'response' => 'Test comment'),
            array(
                'status' => '',
                'comment' => '',
                'response' => '{"error":true,"message":"Comment text cannot be empty."}'
            ),
        );
    }
}
