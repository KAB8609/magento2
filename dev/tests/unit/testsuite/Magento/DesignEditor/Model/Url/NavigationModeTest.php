<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\DesignEditor\Model\Url;

class NavigationModeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test route params
     */
    const FRONT_NAME = 'vde';
    const ROUTE_PATH = 'some-rout-url/page.html';
    const BASE_URL   = 'http://test.com';

    /**
     * @var \Magento\DesignEditor\Model\Url\NavigationMode
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_designHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configInterfaceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_securityInfoMock;

    /**
     * @var array
     */
    protected $_testData = array('themeId' => 1, 'mode' => 'test');

    protected function setUp()
    {
        $this->_designHelperMock = $this->getMock('Magento\DesignEditor\Helper\Data', array(), array(), '', false);
        $this->_coreHelperMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->_storeConfigMock = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $this->_appMock = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);
        $this->_sessionMock = $this->getMock('Magento\Core\Model\Session', array(), array(), '', false);
        $this->_configInterfaceMock = $this->getMock('\Magento\App\Route\ConfigInterface');
        $this->_securityInfoMock = $this->getMock('Magento\Core\Model\Url\SecurityInfoInterface');

        $this->_requestMock->expects($this->any())
            ->method('getAlias')
            ->will($this->returnValueMap(array(
                array('editorMode', 'navigation'),
                array('themeId', 1))));

        $this->_model = new \Magento\DesignEditor\Model\Url\NavigationMode(
            $this->_configInterfaceMock,
            $this->_requestMock,
            $this->_securityInfoMock,
            $this->_designHelperMock,
            $this->_storeConfigMock,
            $this->_coreHelperMock,
            $this->_appMock,
            $this->_storeManagerMock,
            $this->_sessionMock,
            'string',
            $this->_testData
        );
        $this->_model->setRequest($this->_requestMock);
    }

    public function testConstruct()
    {
        $this->assertAttributeEquals($this->_designHelperMock, '_helper', $this->_model);
        $this->assertAttributeEquals($this->_testData, '_data', $this->_model);
    }

    public function testGetRouteUrl()
    {
        $this->_designHelperMock->expects($this->any())
            ->method('getFrontName')
            ->will($this->returnValue(self::FRONT_NAME));

        $store = $this->getMock('Magento\Core\Model\Store',
            array('getBaseUrl', 'isAdmin', 'isAdminUrlSecure', 'isFrontUrlSecure', '__sleep', '__wakeup'),
            array(), '', false
        );
        $store->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue(self::BASE_URL));

        $store->expects($this->any())
            ->method('isAdmin')
            ->will($this->returnValue(false));

        $store->expects($this->any())
            ->method('isAdminUrlSecure')
            ->will($this->returnValue(false));

        $store->expects($this->any())
            ->method('isFrontUrlSecure')
            ->will($this->returnValue(false));

        $this->_model->setData('store', $store);
        $this->_model->setData('type', null);
        $this->_model->setData('route_front_name', self::FRONT_NAME);

        $sourceUrl   = self::BASE_URL . '/' . self::ROUTE_PATH;
        $expectedUrl = self::BASE_URL . '/' . self::FRONT_NAME . '/' . $this->_testData['mode'] . '/'
            . $this->_testData['themeId'] . '/' . self::ROUTE_PATH;

        $this->assertEquals($expectedUrl, $this->_model->getRouteUrl($sourceUrl));
        $this->assertEquals($this->_model, $this->_model->setType(null));
        $this->assertEquals($expectedUrl, $this->_model->getRouteUrl($expectedUrl));
    }
}
