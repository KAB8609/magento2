<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test front name prefix
     */
    const TEST_FRONT_NAME = 'test_front_name';

    /**
     * Test default handle
     */
    const TEST_DEFAULT_HANDLE = 'test_default_handle';

    /**
     * Test disabled cache types
     */
    const TEST_DISABLED_CACHE_TYPES = '<type1 /><type2 />';

    /**
     * Test data for blocks and containers
     */
    const TEST_ELEMENT_DATA = '<node_1>value_1</node_1><node_2>value_2</node_2><node_3>value_3</node_3>';

    /**
     * Test data for date to expire
     */
    const TEST_DATE_TO_EXPIRE = 123;

    /**
     * @var array
     */
    protected $_disabledCacheTypes = array('type1', 'type2');

    /**
     * @var string
     */
    protected $_elementData = array('value_1', 'value_2', 'value_3',);

    /**
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_translatorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_context;

    protected function setUp()
    {
        $this->_translatorMock = $this->getMock('Mage_Core_Model_Translate', array(), array(), '', false);
        $this->_context = new Mage_Core_Helper_Context($this->_translatorMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_context);
    }

    public function testGetFrontName()
    {
        $frontNameNode = new Mage_Core_Model_Config_Element('<test>' . self::TEST_FRONT_NAME . '</test>');

        $configurationMock = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $configurationMock->expects($this->once())
            ->method('getNode')
            ->with(Mage_DesignEditor_Helper_Data::XML_PATH_FRONT_NAME)
            ->will($this->returnValue($frontNameNode));

        $backendSession = $this->getMockBuilder('Mage_Backend_Model_Session')->disableOriginalConstructor()->getMock();

        $this->_model = new Mage_DesignEditor_Helper_Data($this->_context, $configurationMock, $backendSession);
        $this->assertEquals(self::TEST_FRONT_NAME, $this->_model->getFrontName());
    }

    public function testGetDefaultHandle()
    {
        $defaultHandleNode = new Mage_Core_Model_Config_Element('<test>' . self::TEST_DEFAULT_HANDLE . '</test>');

        $configurationMock = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $configurationMock->expects($this->once())
            ->method('getNode')
            ->with(Mage_DesignEditor_Helper_Data::XML_PATH_DEFAULT_HANDLE)
            ->will($this->returnValue($defaultHandleNode));

        $backendSession = $this->getMockBuilder('Mage_Backend_Model_Session')->disableOriginalConstructor()->getMock();

        $this->_model = new Mage_DesignEditor_Helper_Data($this->_context, $configurationMock, $backendSession);
        $this->assertEquals(self::TEST_DEFAULT_HANDLE, $this->_model->getDefaultHandle());
    }

    public function testGetDisabledCacheTypes()
    {
        $cacheTypesNode = new Mage_Core_Model_Config_Element('<test>' . self::TEST_DISABLED_CACHE_TYPES . '</test>');

        $configurationMock = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $configurationMock->expects($this->once())
            ->method('getNode')
            ->with(Mage_DesignEditor_Helper_Data::XML_PATH_DISABLED_CACHE_TYPES)
            ->will($this->returnValue($cacheTypesNode));

        $backendSession = $this->getMockBuilder('Mage_Backend_Model_Session')->disableOriginalConstructor()->getMock();

        $this->_model = new Mage_DesignEditor_Helper_Data($this->_context, $configurationMock, $backendSession);
        $this->assertEquals($this->_disabledCacheTypes, $this->_model->getDisabledCacheTypes());
    }

    /**
     * Test for three similar methods - getBlockWhiteList, getBlockBlackList, getContainerWhiteList
     *
     * @param string $method
     * @param string $xmlPath
     *
     * @dataProvider getElementsListDataProvider
     * @covers Mage_DesignEditor_Helper_Data::getBlockWhiteList
     * @covers Mage_DesignEditor_Helper_Data::getBlockBlackList
     * @covers Mage_DesignEditor_Helper_Data::getContainerWhiteList
     */
    public function testGetElementsList($method, $xmlPath)
    {
        $blockDataNode = new Mage_Core_Model_Config_Element('<test>' . self::TEST_ELEMENT_DATA . '</test>');

        $configurationMock = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $configurationMock->expects($this->once())
            ->method('getNode')
            ->with($xmlPath)
            ->will($this->returnValue($blockDataNode));

        $backendSession = $this->getMockBuilder('Mage_Backend_Model_Session')->disableOriginalConstructor()->getMock();

        $this->_model = new Mage_DesignEditor_Helper_Data($this->_context, $configurationMock, $backendSession);
        $this->assertEquals($this->_elementData, $this->_model->$method());
    }

    /**
     * Data provider for getElementsListDataProvider
     *
     * @return array
     */
    public function getElementsListDataProvider()
    {
        return array(
            'getBlockWhiteList' => array(
                '$method'  => 'getBlockWhiteList',
                '$xmlPath' => Mage_DesignEditor_Helper_Data::XML_PATH_BLOCK_WHITE_LIST,
            ),
            'getBlockBlackList' => array(
                '$method'  => 'getBlockBlackList',
                '$xmlPath' => Mage_DesignEditor_Helper_Data::XML_PATH_BLOCK_BLACK_LIST,
            ),
            'getContainerWhiteList' => array(
                '$method'  => 'getContainerWhiteList',
                '$xmlPath' => Mage_DesignEditor_Helper_Data::XML_PATH_CONTAINER_WHITE_LIST,
            ),
        );
    }

    public function testGetDateToExpire()
    {
        $frontNameNode = new Mage_Core_Model_Config_Element('<test>' . self::TEST_DATE_TO_EXPIRE . '</test>');

        $configurationMock = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $configurationMock->expects($this->once())
            ->method('getNode')
            ->with(Mage_DesignEditor_Helper_Data::XML_PATH_DAYS_TO_EXPIRE)
            ->will($this->returnValue($frontNameNode));

        $backendSession = $this->getMockBuilder('Mage_Backend_Model_Session')->disableOriginalConstructor()->getMock();

        $this->_model = new Mage_DesignEditor_Helper_Data($this->_context, $configurationMock, $backendSession);
        $this->assertEquals(self::TEST_DATE_TO_EXPIRE, $this->_model->getDaysToExpire());
    }
}
