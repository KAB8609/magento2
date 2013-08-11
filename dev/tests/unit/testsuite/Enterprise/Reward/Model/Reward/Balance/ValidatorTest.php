<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

class Enterprise_Reward_Model_Reward_Balance_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Reward_Model_Reward_Balance_Validator
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_orderMock;

    public function setUp()
    {
        $this->_storeManagerMock = $this->getMock('Mage_Core_Model_StoreManager', array(), array(), '', false);
        $this->_modelFactoryMock =
            $this->getMock('Enterprise_Reward_Model_RewardFactory', array('create'), array(), '', false);
        $this->_sessionMock = $this->getMock('Mage_Checkout_Model_Session',
            array('setUpdateSection', 'setGotoSection'), array(), '', false);
        $this->_orderMock =
            $this->getMock('Mage_Sales_Model_Order', array('getRewardPointsBalance'), array(), '', false);
        $this->_model = new Enterprise_Reward_Model_Reward_Balance_Validator(
            $this->_storeManagerMock,
            $this->_modelFactoryMock,
            $this->_sessionMock
        );
    }

    public function testValidateWhenBalanceAboveNull()
    {
        $this->_orderMock->expects($this->any())->method('getRewardPointsBalance')->will($this->returnValue(1));
        $store = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);
        $this->_storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($store));
        $store->expects($this->once())->method('getWebsiteId');
        $reward = $this->getMock('Enterprise_Reward_Model_Reward', array('getPointsBalance'), array(), '', false);
        $this->_modelFactoryMock->expects($this->once())->method('create')->will($this->returnValue($reward));
        $reward->expects($this->once())->method('getPointsBalance')->will($this->returnValue(1));
        $this->_model->validate($this->_orderMock);
    }

    /**
     * @expectedException Enterprise_Reward_Model_Reward_Balance_Exception
     * @expectedExceptionMessage You don't have enough reward points to pay for this purchase.
     */
    public function testValidateWhenBalanceNotEnoughToPlaceOrder()
    {
        $this->_orderMock->expects($this->any())->method('getRewardPointsBalance')->will($this->returnValue(1));
        $store = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);
        $this->_storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($store));
        $store->expects($this->once())->method('getWebsiteId');
        $reward = $this->getMock('Enterprise_Reward_Model_Reward', array('getPointsBalance'), array(), '', false);
        $this->_modelFactoryMock->expects($this->once())->method('create')->will($this->returnValue($reward));
        $reward->expects($this->once())->method('getPointsBalance')->will($this->returnValue(0.5));
        $this->_sessionMock->expects($this->once())->method('setUpdateSection')->with('payment-method');
        $this->_sessionMock->expects($this->once())->method('setGotoSection')->with('payment');

        $this->_model->validate($this->_orderMock);
    }
}
