<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class SalesOrder_CreditMemoTest extends SalesOrder_AbstractTest
{
    /**
     * Remove all created models
     */
    protected function tearDown()
    {
        $this->_restoreIncrementIdPrefix();
        parent::tearDown();
    }

    /**
     * Test sales order credit memo list, info, create, cancel
     *
     * @magentoDataFixture Api/Mage/SalesOrder/_fixture/invoice.php
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        $creditmemoInfo = $this->_createCreditmemo();
        list($product, $qtys, $adjustmentPositive, $adjustmentNegative, $creditMemoIncrementId) = $creditmemoInfo;

        //Test list
        $creditmemoList = Magento_Test_Helper_Api::call($this, 'salesOrderCreditmemoList');
        $this->assertInternalType('array', $creditmemoList);
        $this->assertNotEmpty($creditmemoList, 'Creditmemo list is empty');

        //Test add comment
        $commentText = 'Creditmemo comment';
        $this->assertTrue(
            (bool)Magento_Test_Helper_Api::call(
                $this,
                'salesOrderCreditmemoAddComment',
                array(
                    'creditmemoIncrementId' => $creditMemoIncrementId,
                    'comment' => $commentText
                )
            )
        );

        //Test info
        $creditmemoInfo = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCreditmemoInfo',
            array(
                'creditmemoIncrementId' => $creditMemoIncrementId
            )
        );

        $this->assertInternalType('array', $creditmemoInfo);
        $this->assertNotEmpty($creditmemoInfo);
        $this->assertEquals($creditMemoIncrementId, $creditmemoInfo['increment_id']);

        //Test adjustments fees were added
        $this->assertEquals($adjustmentPositive, $creditmemoInfo['adjustment_positive']);
        $this->assertEquals($adjustmentNegative, $creditmemoInfo['adjustment_negative']);

        //Test order items were refunded
        $this->assertArrayHasKey('items', $creditmemoInfo);
        $this->assertInternalType('array', $creditmemoInfo['items']);
        $this->assertGreaterThan(0, count($creditmemoInfo['items']));

        if (!isset($creditmemoInfo['items'][0])) { // workaround for WSI plain array response
            $creditmemoInfo['items'] = array($creditmemoInfo['items']);
        }

        $this->assertEquals($creditmemoInfo['items'][0]['order_item_id'], $qtys[0]['order_item_id']);
        $this->assertEquals($product->getId(), $creditmemoInfo['items'][0]['product_id']);

        if (!isset($creditmemoInfo['comments'][0])) { // workaround for WSI plain array response
            $creditmemoInfo['comments'] = array($creditmemoInfo['comments']);
        }

        //Test comment was added correctly
        $this->assertArrayHasKey('comments', $creditmemoInfo);
        $this->assertInternalType('array', $creditmemoInfo['comments']);
        $this->assertGreaterThan(0, count($creditmemoInfo['comments']));
        $this->assertEquals($commentText, $creditmemoInfo['comments'][0]['comment']);

        //Test cancel
        //Situation when creditmemo is possible to cancel was not found
        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCreditmemoCancel',
            array('creditmemoIncrementId' => $creditMemoIncrementId)
        );
    }

    /**
     * Test Exception when refund amount greater than available to refund amount
     *
     * @expectedException SoapFault
     * @magentoDataFixture Api/Mage/SalesOrder/_fixture/invoice.php
     * @magentoAppIsolation enabled
     */
    public function testNegativeRefundException()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('order');
        $overRefundAmount = $order->getGrandTotal() + 10;

        Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCreditmemoCreate',
            array(
                'creditmemoIncrementId' => $order->getIncrementId(),
                'creditmemoData' => array(
                    'adjustment_positive' => $overRefundAmount
                )
            )
        );
    }

    /**
     * Test filtered list empty if filter contains incorrect order id
     */
    public function testListEmptyFilter()
    {
        $filter = array('order_id' => 'invalid-id');
        if (self::$_clients[self::$_defaultAdapterCode] instanceof Magento_Test_TestCase_Api_Client_Soap) {
            $filter = array(
                'filter' => array(array('key' => 'order_id', 'value' => 'invalid-id'))
            );
        }

        $creditmemoList = Magento_Test_Helper_Api::call($this, 'salesOrderCreditmemoList', array('filters' => $filter));
        $this->assertEquals(0, count($creditmemoList));
    }

    /**
     * Test Exception on invalid creditmemo create data
     *
     * @expectedException SoapFault
     */
    public function testCreateInvalidOrderException()
    {
        Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCreditmemoCreate',
            array(
                'creditmemoIncrementId' => 'invalid-id',
                'creditmemoData' => array()
            )
        );
    }

    /**
     * Test Exception on invalid credit memo while adding comment
     *
     * @expectedException SoapFault
     */
    public function testAddCommentInvalidOrderException()
    {
        Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCreditmemoAddComment',
            array(
                'creditmemoIncrementId' => 'invalid-id',
                'comment' => 'Comment'
            )
        );
    }

    /**
     * Test Exception on invalid credit memo while getting info
     *
     * @expectedException SoapFault
     */
    public function testInfoInvalidOrderException()
    {
        Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCreditmemoInfo',
            array('creditmemoIncrementId' => 'invalid-id')
        );
    }

    /**
     * Test exception on invalid credit memo cancel
     *
     * @expectedException SoapFault
     */
    public function testCancelInvalidIdException()
    {
        Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCreditmemoCancel',
            array('creditmemoIncrementId' => 'invalid-id')
        );
    }

    /**
     * Test credit memo create API call results
     *
     * @magentoDataFixture Api/Mage/SalesOrder/_fixture/invoice.php
     * @magentoAppIsolation enabled
     */
    public function testAutoIncrementType()
    {
        // Set creditmemo increment id prefix
        $prefix = '01';
        $this->_setIncrementIdPrefix('creditmemo', $prefix);

        $order = self::getFixture('order2');

        $orderItems = $order->getAllItems();
        $qtys = array();

        /** @var $orderItem Mage_Sales_Model_Order_Item */
        foreach ($orderItems as $orderItem) {
            $qtys[] = array('order_item_id' => $orderItem->getId(), 'qty' => 1);
        }
        $adjustmentPositive = 2;
        $adjustmentNegative = 1;
        $data = array(
            'qtys' => $qtys,
            'adjustment_positive' => $adjustmentPositive,
            'adjustment_negative' => $adjustmentNegative
        );
        $orderIncrementalId = $order->getIncrementId();

        //Test create
        $creditMemoIncrementId = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCreditmemoCreate',
            array(
                'creditmemoIncrementId' => $orderIncrementalId,
                'creditmemoData' => $data
            )
        );
        self::setFixture('creditmemoIncrementId', $creditMemoIncrementId);

        $this->assertTrue(is_string($creditMemoIncrementId), 'Increment Id is not a string');
        $this->assertStringStartsWith(
            $prefix,
            $creditMemoIncrementId,
            'Increment Id returned by API is not correct'
        );
    }

    /**
     * Test order creditmemo list. With filters
     *
     * @magentoDataFixture Api/Mage/SalesOrder/_fixture/invoice.php
     * @magentoAppIsolation enabled
     * @depends testCRUD
     */
    public function testListWithFilters()
    {
        $creditmemoInfo = $this->_createCreditmemo();
        list($product, $qtys, $adjustmentPositive, $adjustmentNegative, $creditMemoIncrementId) = $creditmemoInfo;

        /** @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
        $creditmemo = Mage::getModel('Mage_Sales_Model_Order_Creditmemo')->load($creditMemoIncrementId, 'increment_id');

        $filters = array(
            'filters' => array(
                'filter' => array(
                    array('key' => 'state', 'value' => $creditmemo->getData('state')),
                    array('key' => 'created_at', 'value' => $creditmemo->getData('created_at'))
                ),
                'complex_filter' => array(
                    array(
                        'key' => 'creditmemo_id',
                        'value' => array('key' => 'in', 'value' => array($creditmemo->getId(), 0))
                    ),
                )
            )
        );

        $result = Magento_Test_Helper_Api::call($this, 'salesOrderCreditmemoList', $filters);

        if (!isset($result[0])) { // workaround for WS-I
            $result = array($result);
        }
        $this->assertInternalType('array', $result, "Response has invalid format");
        $this->assertEquals(1, count($result), "Invalid creditmemos quantity received");
        foreach (reset($result) as $field => $value) {
            if ($field == 'creditmemo_id') {
                // process field mapping
                $field = 'entity_id';
            }
            $this->assertEquals($creditmemo->getData($field), $value, "Field '{$field}' has invalid value");
        }
    }

    /**
     * Create creditmemo using API. Invoice fixture must be initialized for this method
     *
     * @return array
     */
    protected function _createCreditmemo()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_virtual');

        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('order');

        $orderItems = $order->getAllItems();
        $qtys = array();

        /** @var $orderItem Mage_Sales_Model_Order_Item */
        foreach ($orderItems as $orderItem) {
            $qtys[] = array('order_item_id' => $orderItem->getId(), 'qty' => 1);
        }

        $adjustmentPositive = 2;
        $adjustmentNegative = 3;
        $data = array(
            'qtys' => $qtys,
            'adjustment_positive' => $adjustmentPositive,
            'adjustment_negative' => $adjustmentNegative
        );
        $orderIncrementalId = $order->getIncrementId();

        //Test create
        $creditMemoIncrementId = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCreditmemoCreate',
            array(
                'creditmemoIncrementId' => $orderIncrementalId,
                'creditmemoData' => $data
            )
        );

        /** Add creditmemo to fixtures to ensure that it is removed in teardown. */
        /** @var Mage_Sales_Model_Order_Creditmemo $createdCreditmemo */
        $createdCreditmemo = Mage::getModel('Mage_Sales_Model_Order_Creditmemo');
        $createdCreditmemo->load($creditMemoIncrementId, 'increment_id');
        $this->setFixture('creditmemo', $createdCreditmemo);

        $this->assertNotEmpty($creditMemoIncrementId, 'Creditmemo was not created');
        return array($product, $qtys, $adjustmentPositive, $adjustmentNegative, $creditMemoIncrementId);
    }
}
