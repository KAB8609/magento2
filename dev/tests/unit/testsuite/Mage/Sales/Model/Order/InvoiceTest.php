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

class Mage_Sales_Model_Order_InvoiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sales_Model_Order_Invoice
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Mage_Sales_Model_Order
     */
    protected $_orderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Mage_Sales_Model_Order_Payment
     */
    protected $_paymentMock;

    public function setUp()
    {
        $helperManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_orderMock = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('getPayment'))
            ->getMock();
        $this->_paymentMock = $this->getMockBuilder('Mage_Sales_Model_Order_Payment')
            ->disableOriginalConstructor()
            ->setMethods(array('canVoid'))
            ->getMock();

        $this->_model = $helperManager->getObject('Mage_Sales_Model_Order_Invoice', array());
        $this->_model->setOrder($this->_orderMock);
    }

    /**
     * @dataProvider canVoidDataProvider
     * @param bool $canVoid
     */
    public function testCanVoid($canVoid)
    {
        $this->_orderMock->expects($this->once())->method('getPayment')->will($this->returnValue($this->_paymentMock));
        $this->_paymentMock->expects($this->once())
            ->method('canVoid')
            ->with($this->equalTo($this->_model))
            ->will($this->returnValue($canVoid));

        $this->_model->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID);
        $this->assertEquals($canVoid, $this->_model->canVoid());
    }

    /**
     * @dataProvider canVoidDataProvider
     * @param bool $canVoid
     */
    public function testDefaultCanVoid($canVoid)
    {
        $this->_model->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID);
        $this->_model->setCanVoidFlag($canVoid);

        $this->assertEquals($canVoid, $this->_model->canVoid());
    }

    public function canVoidDataProvider()
    {
        return array(
            array(true),
            array(false)
        );
    }
}
