<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Widget_Model_WidgetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Model_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storage;

    /**
     * @var Mage_Widget_Model_Widget_Mapper|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_xmlMapper;

    /**
     * @var Mage_Widget_Model_Widget
     */
    protected $_model;

    public function setUp()
    {
        $this->_storage = $this->getMockBuilder('Mage_Widget_Model_Config_Data')
            ->disableOriginalConstructor()
            ->getMock();
        $viewUrl = $this->getMockBuilder('Mage_Core_Model_View_Url')
            ->disableOriginalConstructor()
            ->getMock();
        $viewFileSystem = $this->getMockBuilder('Mage_Core_Model_View_FileSystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_xmlMapper = $this->getMockBuilder('Mage_Widget_Model_Widget_Mapper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = new Mage_Widget_Model_Widget($this->_storage, $viewUrl, $viewFileSystem, $this->_xmlMapper);
    }

    public function testGetWidgets()
    {
        $expected = array('val1', 'val2');
        $this->_storage->expects($this->once())->method('get')
            ->will($this->returnValue($expected));
        $this->_xmlMapper->expects($this->once())->method('map')
            ->will($this->returnValue($expected));
        $result = $this->_model->getWidgets();
        $this->assertEquals($expected, $result);
    }

    public function testGetWidgetByClassType()
    {
        $widget1 = array(
            '@' => array(
                'type' => 'type1',
                'translate' => 'label'
            )
        );
        $widgets = array(
            'widget1' => $widget1
        );
        $this->_storage->expects($this->any())->method('get')
            ->will($this->returnValue($widgets));
        $this->_xmlMapper->expects($this->any())->method('map')
            ->will($this->returnValue($widgets));
        $this->assertEquals($widget1, $this->_model->getWidgetByClassType('type1'));
        $this->assertNull($this->_model->getWidgetByClassType('type2'));
    }
}
