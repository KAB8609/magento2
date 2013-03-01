<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Event_Observer
     */
    protected $_observer;

    /**
     * @var Mage_Catalog_Model_Observer
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $this->_model = new Mage_Catalog_Model_Observer();
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
    }

    public function testTransitionProductTypeSimple()
    {
        $product = new Varien_Object(array('type_id' => 'simple'));
        $this->_observer = new Varien_Event_Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('simple', $product->getTypeId());
    }

    public function testTransitionProductTypeVirtual()
    {
        $product = new Varien_Object(array('type_id' => 'virtual', 'is_virtual' => ''));
        $this->_observer = new Varien_Event_Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('virtual', $product->getTypeId());
    }

    public function testTransitionProductTypeSimpleToVirtual()
    {
        $product = new Varien_Object(array('type_id' => 'simple', 'is_virtual' => ''));
        $this->_observer = new Varien_Event_Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('virtual', $product->getTypeId());
    }

    public function testTransitionProductTypeVirtualToSimple()
    {
        $product = new Varien_Object(array('type_id' => 'virtual'));
        $this->_observer = new Varien_Event_Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('simple', $product->getTypeId());
    }
}
