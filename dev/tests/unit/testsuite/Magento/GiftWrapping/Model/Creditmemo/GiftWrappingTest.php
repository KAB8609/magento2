<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Model;

/**
 * Test class for \Magento\GiftWrapping\Model
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    public function testCreditMemoItemWrapping()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $model = $objectHelper->getObject(
            'Magento\GiftWrapping\Model\Total\Creditmemo\Giftwrapping',
            array()
        );


        $creditmemo = $this->getMockBuilder('Magento\Sales\Model\Order\CreditMemo')
            ->disableOriginalConstructor()
            ->setMethods(array('getAllItems', 'getOrder', 'setBaseGrandTotal', 'getBaseGrandTotal',
                'getGwBasePrice', 'getGwCardBasePrice', 'setGrandTotal', 'getGrandTotal',
                'getGwItemsPrice', 'getGwPrice', 'getGwCardPrice', 'setBaseCustomerBalanceReturnMax',
                'getBaseCustomerBalanceReturnMax', 'setCustomerBalanceReturnMax', 'getCustomerBalanceReturnMax',
                'setGwItemsBasePrice', 'setGwItemsPrice', 'getGwItemsBasePrice'
                ))
            ->getMock();

        $item = new \Magento\Object();
        $orderItem = new \Magento\Object(
            array('gw_id' => 1, 'gw_base_price_invoiced' => 5, 'gw_price_invoiced' => 10)
        );

        $item->setQty(2)
             ->setOrderItem($orderItem);
        $order = new \Magento\Object();

        $creditmemo->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue(array($item)));

        $creditmemo->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($order));

        $creditmemo->expects($this->once())
            ->method('setGwItemsBasePrice')
            ->with(10);
        $creditmemo->expects($this->once())
            ->method('setGwItemsPrice')
            ->with(20);

        $model->collect($creditmemo);

    }
}