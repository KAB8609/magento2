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

/**
 * Test class for \Magento\Core\Model\Layout\Factory
 */
class Magento_Core_Model_Layout_FactoryTest extends PHPUnit_Framework_TestCase
{
    /*
     * Test class name
     */
    const CLASS_NAME  = '\Magento\Core\Model\Layout';

    /**
     * Test arguments
     *
     * @var array
     */
    protected $_arguments = array();

    /**
     * ObjectManager mock for tests
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * Test class instance
     *
     * @var \Magento\Core\Model\Layout\Factory
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\Core\Model\Layout\Factory($this->_objectManager);
    }

    public function testConstruct()
    {
        $this->assertAttributeInstanceOf('\Magento\ObjectManager', '_objectManager', $this->_model);
    }

    public function testCreateLayoutNew()
    {
        $modelLayout = $this->getMock(self::CLASS_NAME, array(), array(), '', false);

        $this->_objectManager->expects($this->once())
            ->method('configure')
            ->with(array(self::CLASS_NAME => array('parameters' => array('someParam' => 'someVal'))));

        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with(\Magento\Core\Model\Layout\Factory::CLASS_NAME)
            ->will($this->returnValue($modelLayout));

        $this->assertEquals($modelLayout, $this->_model->createLayout(array('someParam' => 'someVal')));
    }
}
