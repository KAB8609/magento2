<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class Mage_Core_Controller_Varien_Action_Factory
 */
class Mage_Core_Controller_Varien_Action_FactoryTest extends PHPUnit_Framework_TestCase
{
    /*
    * Test controller class name
    */
    const CONTROLLER_NAME  = 'TestController';

    /**
     * ObjectManager mock for tests
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Test class instance
     *
     * @var Mage_Core_Controller_Varien_Action_Factory
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
    }

    public function testConstruct()
    {
        $this->_model = new Mage_Core_Controller_Varien_Action_Factory($this->_objectManager);
        $this->assertAttributeInstanceOf('Magento_ObjectManager', '_objectManager', $this->_model);
    }

    public function testCreateController()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with(self::CONTROLLER_NAME, array())
            ->will($this->returnValue('TestControllerInstance'));

        $this->_model = new Mage_Core_Controller_Varien_Action_Factory($this->_objectManager);
        $this->assertEquals('TestControllerInstance', $this->_model->createController(self::CONTROLLER_NAME));
    }
}
