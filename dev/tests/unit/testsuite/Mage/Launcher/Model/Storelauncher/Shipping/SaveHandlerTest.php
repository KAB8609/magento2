<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Storelauncher_Shipping_SaveHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_Storelauncher_Shipping_SaveHandler
     */
    protected $_saveHandler;

    protected function setUp()
    {
        // Mock core configuration model
        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        // Mock shipping save handler factory
        $saveHandlerFactory = $this->getMock('Mage_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactory',
            array(), array(), '', false);
        // Mock backend config model
        $backendConfigModel = $this->getMock('Mage_Backend_Model_Config', array(), array(), '', false);
        $this->_saveHandler = new Mage_Launcher_Model_Storelauncher_Shipping_SaveHandler(
            $config,
            $backendConfigModel,
            $saveHandlerFactory
        );
    }

    protected function tearDown()
    {
        $this->_saveHandler = null;
    }

    public function testGetRelatedShippingMethods()
    {
        $expectedMethods = array(
            'carriers_flatrate',
            'carriers_ups',
            'carriers_usps',
            'carriers_fedex',
            'carriers_dhlint',
        );
        $this->assertEquals($expectedMethods, $this->_saveHandler->getRelatedShippingMethods());
    }

    public function testSaveShippingMethod()
    {
        $data = array('shipping_method' => 'carriers_flatrate');
        // Mock shipping save handler
        $shippingSaveHandler = $this->getMock(
            'Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_FlatrateSaveHandler',
            array(),
            array(),
            '',
            false
        );

        // Mock backend config model
        $backendConfigModel = $this->getMock('Mage_Backend_Model_Config', array(), array(), '', false);

        $shippingSaveHandler->expects($this->once())
            ->method('save')
            ->with($data)
            ->will($this->returnValue($shippingSaveHandler));

        // Mock shipping save handler factory
        $saveHandlerFactory = $this->getMock('Mage_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactory',
            array('create'), array(), '', false);

        $saveHandlerFactory->expects($this->once())
            ->method('create')
            ->with('carriers_flatrate')
            ->will($this->returnValue($shippingSaveHandler));

        // Mock core configuration model
        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);

        $saveHandler = new Mage_Launcher_Model_Storelauncher_Shipping_SaveHandler(
            $config,
            $backendConfigModel,
            $saveHandlerFactory
        );
        $saveHandler->saveShippingMethod($data);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Illegal shipping method ID specified.
     */
    public function testSaveShippingMethodThrowsExceptionWhenShippingMethodHasIllegalId()
    {
        $this->_saveHandler->saveShippingMethod(array('shipping_method' => 'wrong_id'));
    }
}