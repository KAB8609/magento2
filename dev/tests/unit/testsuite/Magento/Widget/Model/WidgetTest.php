<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Model;

class WidgetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Model\Config\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storage;

    /**
     * @var \Magento\Widget\Model\Widget
     */
    protected $_model;

    public function setUp()
    {
        $this->_storage = $this->getMockBuilder('Magento\Widget\Model\Config\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $viewUrl = $this->getMockBuilder('Magento\View\Url')
            ->disableOriginalConstructor()
            ->getMock();
        $viewFileSystem = $this->getMockBuilder('Magento\View\FileSystem')
            ->disableOriginalConstructor()
            ->getMock();
        $coreData = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = new \Magento\Widget\Model\Widget($coreData, $this->_storage, $viewUrl, $viewFileSystem);
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $objectManagerHelper->getObject('Magento\Widget\Model\Widget', array('dataStorage' => $this->_storage));
        $this->_model = $objectManagerHelper->getObject('Magento\Widget\Model\Widget', array(
            'dataStorage' => $this->_storage)
        );
    }

    public function testGetWidgets()
    {
        $expected = array('val1', 'val2');
        $this->_storage->expects($this->once())->method('get')
            ->will($this->returnValue($expected));
        $result = $this->_model->getWidgets();
        $this->assertEquals($expected, $result);
    }

    public function testGetWidgetsWithFilter()
    {
        $configFile = __DIR__ . '/_files/mappedConfigArrayAll.php';
        $widgets = include $configFile;
        $this->_storage->expects($this->once())->method('get')
            ->will($this->returnValue($widgets));
        $result = $this->_model->getWidgets(array(
            'name' => 'CMS Page Link',
            'description' => 'Link to a CMS Page',));
        $configFileOne = __DIR__ . '/_files/mappedConfigArray1.php';
        $expected = array('cms_page_link' => include $configFileOne);
        $this->assertEquals($expected, $result);
    }

    public function testGetWidgetsWithUnknownFilter()
    {
        $configFile = __DIR__ . '/_files/mappedConfigArrayAll.php';
        $widgets = include $configFile;
        $this->_storage->expects($this->once())->method('get')
            ->will($this->returnValue($widgets));
        $result = $this->_model->getWidgets(array(
            'name' => 'unknown',
            'description' => 'unknown',));
        $expected = array();
        $this->assertEquals($expected, $result);
    }

    public function testGetWidgetByClassType()
    {
        $widgetOne = array(
            '@' => array(
                'type' => 'type1',
            )
        );
        $widgets = array(
            'widget1' => $widgetOne
        );
        $this->_storage->expects($this->any())->method('get')
            ->will($this->returnValue($widgets));
        $this->assertEquals($widgetOne, $this->_model->getWidgetByClassType('type1'));
        $this->assertNull($this->_model->getWidgetByClassType('type2'));
    }
}
