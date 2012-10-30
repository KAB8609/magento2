<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Sales_Model_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testAfterCommitCallbackOrderGrid()
    {
        $collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Grid_Collection');
        $this->assertEquals(1, $collection->count());
        foreach ($collection as $order) {
            $this->assertInstanceOf('Mage_Sales_Model_Order', $order);
            $this->assertEquals('100000001', $order->getIncrementId());
        }
    }

    public function testAfterCommitCallbackOrderGridNotInvoked()
    {
        $adapter = Mage::getResourceSingleton('Mage_Core_Model_Resource')->getConnection('write');
        $this->assertEquals(0, $adapter->getTransactionLevel(), 'This test must be outside a transaction.');

        $localOrderModel = Mage::getModel('Mage_Sales_Model_Order');
        $resource = $localOrderModel->getResource();
        $resource->beginTransaction();
        try {
            /** @var $order Mage_Sales_Model_Order */
            require __DIR__ . '/../_files/order.php';
            $collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Grid_Collection');
            $this->assertEquals(0, $collection->count());
            $resource->rollBack();
        } catch (Exception $e) {
            $resource->rollBack();
            throw $e;
        }
    }
}
