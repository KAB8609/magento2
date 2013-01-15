<?php
/**
 * Invoice API model test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDbIsolation enabled
 */
class Mage_Sales_Model_Order_Invoice_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test create and read created invoice
     *
     * @magentoDataFixture Mage/Sales/Model/Order/Api/_files/order.php
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');
        $incrementId = $order->getIncrementId();

        // Create new invoice
        $newInvoiceId = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderInvoiceCreate',
            array(
                'orderIncrementId' => $incrementId,
                'itemsQty' => array(),
                'comment' => 'invoice Created',
                'email' => true,
                'includeComment' => true
            )
        );
        $this->assertNotNull($newInvoiceId);
        Mage::register('invoiceIncrementId', $newInvoiceId);

        // View new invoice
        $invoice = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderInvoiceInfo',
            array(
                'invoiceIncrementId' => $newInvoiceId
            )
        );

        $this->assertEquals($newInvoiceId, $invoice['increment_id']);
    }

    /**
     * Test credit memo create API call results
     *
     * @magentoDataFixture Mage/Sales/Model/Order/Api/_files/order.php
     * @magentoAppIsolation enabled
     */
    public function testAutoIncrementType()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $order = Mage::registry('order2');
        $incrementId = $order->getIncrementId();

        // Set invoice increment id prefix
        $prefix = '01';
        Magento_Test_Helper_Eav::setIncrementIdPrefix('invoice', $prefix);

        // Create new invoice
        $newInvoiceId = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderInvoiceCreate',
            array(
                'orderIncrementId' => $incrementId,
                'itemsQty' => array(),
                'comment' => 'invoice Created',
                'email' => true,
                'includeComment' => true
            )
        );

        $this->assertTrue(is_string($newInvoiceId), 'Increment Id is not a string');
        $this->assertStringStartsWith($prefix, $newInvoiceId, 'Increment Id returned by API is not correct');
        Mage::register('invoiceIncrementId', $newInvoiceId);
    }

    /**
     * Test order invoice list. With filters
     *
     * @magentoDataFixture Mage/Sales/Model/Order/Api/_files/invoice.php
     * @magentoAppIsolation enabled
     */
    public function testListWithFilters()
    {
        /** @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = Mage::registry('invoice');

        $filters = array(
            'filters' => (object)array(
                'filter' => array(
                    (object)array('key' => 'state', 'value' => $invoice->getData('state')),
                    (object)array('key' => 'created_at', 'value' => $invoice->getData('created_at'))
                ),
                'complex_filter' => array(
                    (object)array(
                        'key' => 'invoice_id',
                        'value' => (object)array('key' => 'in', 'value' => array($invoice->getId(), 0))
                    ),
                )
            )
        );

        $result = Magento_Test_Helper_Api::call($this, 'salesOrderInvoiceList', $filters);

        if (!isset($result[0])) { // workaround for WS-I
            $result = array($result);
        }
        $this->assertInternalType('array', $result, "Response has invalid format");
        $this->assertEquals(1, count($result), "Invalid invoices quantity received");
        foreach (reset($result) as $field => $value) {
            if ($field == 'invoice_id') {
                // process field mapping
                $field = 'entity_id';
            }
            $this->assertEquals($invoice->getData($field), $value, "Field '{$field}' has invalid value");
        }
    }
}
