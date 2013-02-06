<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Test_Annotation_AppIsolation.
 */
class Magento_Test_Annotation_AppIsolationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Annotation_AppIsolation|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var Magento_Test_Application|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_application;

    protected function setUp()
    {
        $this->_application = $this->getMock('Magento_Test_Application', array('reinitialize'), array(), '', false);
        $this->_object = $this->getMock(
            'Magento_Test_Annotation_AppIsolation', array('_cleanupCache'), array($this->_application)
        );
    }

    protected function tearDown()
    {
        $this->_application = null;
        $this->_object = null;
    }

    public function testStartTestSuite()
    {
        $this->_application->expects($this->once())->method('reinitialize');
        $this->_object->startTestSuite();
    }

    /**
     * @magentoAppIsolation invalid
     * @expectedException Magento_Exception
     */
    public function testEndTestIsolationInvalid()
    {
        $this->_object->endTest($this);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppIsolation disabled
     * @expectedException Magento_Exception
     */
    public function testEndTestIsolationAmbiguous()
    {
        $this->_object->endTest($this);
    }

    public function testEndTestIsolationDefault()
    {
        $this->_application->expects($this->never())->method('reinitialize');
        $this->_object->endTest($this);
    }

    public function testEndTestIsolationController()
    {
        /** @var $controllerTest Magento_Test_TestCase_ControllerAbstract */
        $controllerTest = $this->getMockForAbstractClass('Magento_Test_TestCase_ControllerAbstract');
        $this->_application->expects($this->once())->method('reinitialize');
        $this->_object->endTest($controllerTest);
    }

    /**
     * @magentoAppIsolation disabled
     */
    public function testEndTestIsolationDisabled()
    {
        $this->_application->expects($this->never())->method('reinitialize');
        $this->_object->endTest($this);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testEndTestIsolationEnabled()
    {
        $this->_application->expects($this->once())->method('reinitialize');
        $this->_object->endTest($this);
    }
}
