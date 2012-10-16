<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Layout_Argument_Updater
 */
class Mage_Core_Model_Layout_Argument_UpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_Argument_Updater
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_argUpdaterMock;

    protected function setUp()
    {
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);

        $this->_argUpdaterMock = $this->getMock(
            'Mage_Core_Model_Layout_Argument_UpdaterInterface',
            array(),
            array(),
            '',
            false);

        $this->_model = new Mage_Core_Model_Layout_Argument_Updater(array(
            'objectFactory' => $this->_objectFactoryMock,
        ));
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_argUpdaterMock);
        unset($this->_objectFactoryMock);
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithInvalidObjectFactory()
    {
        new Mage_Core_Model_Layout_Argument_Updater(array('objectFactory' => new StdClass()));
    }

    public function testApplyUpdatersWithValidUpdaters()
    {
        $value = 1;

        $this->_objectFactoryMock->expects($this->exactly(2))
            ->method('getModelInstance')
            ->with($this->logicalOr('Dummy_Updater_1', 'Dummy_Updater_2'))
            ->will($this->returnValue($this->_argUpdaterMock));

        $this->_argUpdaterMock->expects($this->exactly(2))
            ->method('update')
            ->with($value)
            ->will($this->returnValue($value));

        $updaters = array('Dummy_Updater_1', 'Dummy_Updater_2');
        $this->assertEquals($value, $this->_model->applyUpdaters($value, $updaters));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testApplyUpdatersWithInvalidUpdaters()
    {
        $this->_objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with('Dummy_Updater_1')
            ->will($this->returnValue(new StdClass()));
        $updaters = array('Dummy_Updater_1', 'Dummy_Updater_2');

        $this->_model->applyUpdaters(1, $updaters);
    }
}
