<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_View_Design_ProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_View_Design_Proxy
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_View_DesignInterface
     */
    protected $_viewDesign;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_viewDesign = $this->getMock('Mage_Core_Model_View_DesignInterface');
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with('Mage_Core_Model_View_Design')
            ->will($this->returnValue($this->_viewDesign));
        $this->_model = new Mage_Core_Model_View_Design_Proxy($this->_objectManager);
    }

    protected function tearDown()
    {
        $this->_objectManager = null;
        $this->_model = null;
        $this->_viewDesign = null;
    }

    public function testGetDesignParams()
    {
        $this->_viewDesign->expects($this->once())
            ->method('getDesignParams')
            ->will($this->returnValue('return value'));
        $this->assertSame('return value', $this->_model->getDesignParams());
    }
}
