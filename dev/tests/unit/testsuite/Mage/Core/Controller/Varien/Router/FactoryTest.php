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
 * Test class Mage_Core_Controller_Varien_Router_Factory
 */
class Mage_Core_Controller_Varien_Router_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**#@+
    * Test arguments
    */
    const CLASS_NAME  = 'TestClass';
    const AREA  = 'TestArea';
    const BASE_CONTROLLER  = 'TestBaseController';
    /**#@-*/

    /**
     * ObjectManager mock for tests
     *
     * @var Magento_ObjectManager_Zend
     */
    protected $_objectManager;

    /**
     * Test class instance
     *
     * @var Mage_Core_Controller_Varien_Router_Factory
     */
    protected $_controller;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_controller = new Mage_Core_Controller_Varien_Router_Factory($this->_objectManager);
    }

    public function testConstruct()
    {
        $this->assertAttributeInstanceOf('Magento_ObjectManager', '_objectManager', $this->_controller);
    }

    public function testCreateRouterNoArguments()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with(self::CLASS_NAME)
            ->will($this->returnValue('TestRouterInstance'));

        $this->assertEquals('TestRouterInstance', $this->_controller->createRouter(self::CLASS_NAME));
    }

    public function testCreateRouterWithArguments()
    {
        $arguments = array(
            'areaCode'       => self::AREA,
            'baseController' => self::BASE_CONTROLLER,
        );

        $routerInfo = array(
            'area'            => self::AREA,
            'base_controller' => self::BASE_CONTROLLER,
        );

        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with(self::CLASS_NAME, $arguments)
            ->will($this->returnValue('TestRouterInstance'));

        $this->assertEquals('TestRouterInstance', $this->_controller->createRouter(self::CLASS_NAME, $routerInfo));
    }
}
