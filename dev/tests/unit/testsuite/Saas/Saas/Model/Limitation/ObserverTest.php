<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Saas_Model_Limitation_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelSpecificationChainMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_saasHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Saas_Saas_Model_Limitation_Observer
     */
    protected $_modelObserver;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_modelSpecificationChainMock = $this->getMock('Saas_Saas_Model_Limitation_Specification_Chain', array(),
            array(), '', false);
        $this->_saasHelperMock = $this->getMock('Saas_Saas_Helper_Data', array(), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelObserver = $objectManagerHelper->getObject(
            'Saas_Saas_Model_Limitation_Observer',
            array(
                'request' => $this->_requestMock,
                'specification' => $this->_modelSpecificationChainMock,
                'saasHelper' => $this->_saasHelperMock,
            )
        );
    }

    public function testLimitFunctionality()
    {
        $this->_modelSpecificationChainMock->expects($this->once())->method('isAllowed')->with($this->_requestMock)
            ->will($this->returnValue(false));
        $this->_saasHelperMock->expects($this->once())->method('customizeNoRoutForward')->with($this->_requestMock);

        $this->_modelObserver->limitFunctionality($this->_eventObserverMock);
    }

    public function testNonLimitFunctionality()
    {
        $this->_modelSpecificationChainMock->expects($this->once())->method('isAllowed')->with($this->_requestMock)
            ->will($this->returnValue(true));
        $this->_saasHelperMock->expects($this->never())->method('customizeNoRoutForward');

        $this->_modelObserver->limitFunctionality($this->_eventObserverMock);
    }
}
