<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Event_Config_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Event_Config_Data
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento_Core_Model_Event_Config_Reader', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $this->_appStateMock = $this->getMock('Magento_Core_Model_App_State', array(), array(), '', false);
        $this->_model = new Magento_Core_Model_Event_Config_Data(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            $this->_appStateMock
        );
    }

    public function testGet()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $this->_configScopeMock->expects($this->once())->method('getCurrentScope')->will($this->returnValue('value'));
        $this->assertEquals(null, $this->_model->get());
    }
}