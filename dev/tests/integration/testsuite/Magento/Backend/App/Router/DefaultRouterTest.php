<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\App\Router;

/**
 * @magentoAppArea adminhtml
 */
class DefaultRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\App\Router\DefaultRouter
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_routeConfigMock;

    protected function setUp()
    {
        parent::setUp();

        $this->_routeConfigMock = $this->getMock('Magento\App\Route\Config', array(), array(), '', false);
        $options = array(
            'areaCode'        => \Magento\Core\Model\App\Area::AREA_ADMINHTML,
            'baseController'  => 'Magento\Backend\Controller\AbstractAction',
            'routeConfig' => $this->_routeConfigMock
        );
        $this->_frontMock = $this->getMock('Magento\App\FrontController', array(), array(), '', false);
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\App\Router\DefaultRouter', $options);
        $this->_model->setFront($this->_frontMock);
    }

    public function testRouterCannotProcessRequestsWithWrongFrontName()
    {
        $request = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('frontend/admin/dashboard'));
        $this->_frontMock->expects($this->never())
            ->method('setDefault');
        $this->_model->match($request);
    }

    public function testRouterCanProcessRequestsWithProperFrontName()
    {
        $request = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('backend/admin/dashboard'));
        $this->_frontMock->expects($this->once())
            ->method('setDefault');

        $adminRoute = array(
            'adminhtml' => array(
                'id'        => 'adminhtml',
                'frontName' => 'admin',
                'modules'   => array(
                    'Magento_Adminhtml'
                )
            )
        );

        $this->_routeConfigMock->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($adminRoute));
        $this->_model->match($request);
    }

    public function testFetchDefault()
    {
        $default = array(
            'area' => '',
            'module' => 'admin',
            'controller' => 'index',
            'action' => 'index'
        );
        $routes = array(
            'adminhtml' => array(
                'id' => 'adminhtml',
                'frontName' => 'admin',
                'modules' => array()
            ),
            'key1' => array('frontName' => 'something'),
        );

        $this->_routeConfigMock->expects($this->once())->method('getRoutes')
            ->will($this->returnValue($routes));

        $this->_frontMock->expects($this->once())
            ->method('setDefault')
            ->with($this->equalTo($default));
        $this->_model->fetchDefault();
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $className
     *
     * @dataProvider getControllerClassNameDataProvider
     */
    public function testGetControllerClassName($module, $controller, $className)
    {
        $this->assertEquals($className, $this->_model->getControllerClassName($module, $controller));
    }

    public function getControllerClassNameDataProvider()
    {
        return array(
            array('Magento_Adminhtml', 'index', 'Magento\Adminhtml\Controller\Index'),
            array('Magento_Index', 'process', 'Magento\Index\Controller\Adminhtml\Process'),
            array('Magento_Index_Adminhtml', 'process', 'Magento\Index\Controller\Adminhtml\Process'),
        );
    }
}
