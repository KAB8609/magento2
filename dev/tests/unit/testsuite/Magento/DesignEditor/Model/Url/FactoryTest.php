<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_DesignEditor_Model_Url_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_DesignEditor_Model_Url_Factory
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_model = new Magento_DesignEditor_Model_Url_Factory($this->_objectManager);
    }

    public function testConstruct()
    {
        $this->assertAttributeInstanceOf('Magento_ObjectManager', '_objectManager', $this->_model);
    }

    public function testReplaceClassName()
    {
        $this->_objectManager->expects($this->once())
            ->method('configure')
            ->with(array('preferences' => array('Magento_Core_Model_Url' => 'TestClass')));

        $this->assertEquals($this->_model, $this->_model->replaceClassName('TestClass'));
    }

    public function testCreate()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento_Core_Model_Url', array())
            ->will($this->returnValue('ModelInstance'));

        $this->assertEquals('ModelInstance', $this->_model->create());
    }
}