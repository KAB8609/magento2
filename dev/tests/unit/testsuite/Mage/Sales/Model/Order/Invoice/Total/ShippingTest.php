<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Model_Order_Invoice_Total_ShippingTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve new invoice collection from an array of invoices' data
     *
     * @param array $invoicesData
     * @return Varien_Data_Collection
     */
    protected function _getInvoiceCollection(array $invoicesData)
    {
        $result = new Varien_Data_Collection();
        foreach ($invoicesData as $oneInvoiceData) {
            /** @var $prevInvoice Mage_Sales_Model_Order_Invoice */
            $prevInvoice = $this->getMock('Mage_Sales_Model_Order_Invoice', array('_init'), array($oneInvoiceData));
            $result->addItem($prevInvoice);
        }
        return $result;
    }

    /**
     * @dataProvider collectDataProvider
     * @param array $prevInvoicesData
     * @param float $orderShipping
     * @param float $invoiceShipping
     * @param float $expectedShipping
     */
    public function testCollect(array $prevInvoicesData, $orderShipping, $invoiceShipping, $expectedShipping)
    {
        /** @var $order Mage_Sales_Model_Order|PHPUnit_Framework_MockObject_MockObject */
        $order = $this->getMock('Mage_Sales_Model_Order', array('_init', 'getInvoiceCollection'));
        $order->setData('shipping_amount', $orderShipping);
        $order->expects($this->any())
            ->method('getInvoiceCollection')
            ->will($this->returnValue($this->_getInvoiceCollection($prevInvoicesData)))
        ;
        /** @var $invoice Mage_Sales_Model_Order_Invoice|PHPUnit_Framework_MockObject_MockObject */
        $invoice = $this->getMock('Mage_Sales_Model_Order_Invoice', array('_init'));
        $invoice->setData('shipping_amount', $invoiceShipping);
        $invoice->setOrder($order);

        $total = new Mage_Sales_Model_Order_Invoice_Total_Shipping();
        $total->collect($invoice);

        $this->assertEquals($expectedShipping, $invoice->getShippingAmount());
    }

    public static function collectDataProvider()
    {
        return array(
            'no previous invoices' => array(
                'prevInvoicesData' => array(array()),
                'orderShipping'    => 10.00,
                'invoiceShipping'  => 5.00,
                'expectedShipping' => 10.00
            ),
            'zero shipping in previous invoices' => array(
                'prevInvoicesData' => array(array('shipping_amount' => '0.0000')),
                'orderShipping'    => 10.00,
                'invoiceShipping'  => 5.00,
                'expectedShipping' => 10.00
            ),
            'non-zero shipping in previous invoices' => array(
                'prevInvoicesData' => array(array('shipping_amount' => '10.000')),
                'orderShipping'    => 10.00,
                'invoiceShipping'  => 5.00,
                'expectedShipping' => 0
            ),
        );
    }
}
