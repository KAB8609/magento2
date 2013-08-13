<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Model_RmaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Enterprise/Rma/_files/order.php
     */
    public function testSaveRma()
    {
        $order = Mage::getModel('Mage_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $rma = Mage::getModel('Enterprise_Rma_Model_Rma');
        $rmaItems = array();

        foreach ($order->getItemsCollection() as $item) {
            $rmaItems[] = array(
                'order_item_id' => $item->getId(),
                'qty_requested' => '1' ,
                'resolution' => '3' ,
                'condition' => '6' ,
                'reason' => '10' ,
            );
        }
        $data = array(
            'customer_custom_email' => '',
            'items' => $rmaItems,
            'rma_comment' => 'comment',
        );
        $rmaData = array(
            'status'                => Enterprise_Rma_Model_Rma_Source_Status::STATE_PENDING,
            'date_requested'        => Mage::getSingleton('Magento_Core_Model_Date')->gmtDate(),
            'order_id'              => $order->getId(),
            'order_increment_id'    => $order->getIncrementId(),
            'store_id'              => $order->getStoreId(),
            'customer_id'           => $order->getCustomerId(),
            'order_date'            => $order->getCreatedAt(),
            'customer_name'         => $order->getCustomerName(),
            'customer_custom_email' => 'example@domain.com'
        );

        $rma->setData($rmaData)->saveRma($data);
        $rmaId = $rma->getId();

        unset($rma);
        $rma = Mage::getModel('Enterprise_Rma_Model_Rma');
        $rma->load($rmaId);
        $this->assertEquals($rma->getId(), $rmaId);
        $this->assertEquals($rma->getOrderId(), $order->getId());
        $this->assertEquals($rma->getCustomerCustomEmail(), $rmaData['customer_custom_email']);
        $this->assertEquals($rma->getOrderIncrementId(), $order->getIncrementId());
        $this->assertEquals($rma->getStoreId(), $order->getStoreId());
        $this->assertEquals($rma->getStatus(), Enterprise_Rma_Model_Rma_Source_Status::STATE_PENDING);
    }
}
