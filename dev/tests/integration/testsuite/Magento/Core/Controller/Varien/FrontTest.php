<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Controller\Varien;

class FrontTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Controller\Varien\Front
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $this->_objectManager->create('Magento\Core\Controller\Varien\Front');
    }

    public function testSetGetDefault()
    {
        $this->_model->setDefault('test', 'value');
        $this->assertEquals('value', $this->_model->getDefault('test'));

        $default = array('some_key' => 'some_value');
        $this->_model->setDefault($default);
        $this->assertEquals($default, $this->_model->getDefault());
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf('Magento\Core\Controller\Request\Http', $this->_model->getRequest());
    }

    public function testGetResponse()
    {
        \Mage::app()->setResponse(\Mage::getSingleton('Magento\Core\Controller\Response\Http'));
        if (!\Magento\TestFramework\Helper\Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test get response without sending headers');
        }
        $this->assertInstanceOf('Magento\Core\Controller\Response\Http', $this->_model->getResponse());
    }

    public function testGetRouter()
    {
        $this->assertInstanceOf('Magento\Core\Controller\Varien\Router\DefaultRouter',
            $this->_model->getRouter('default'));
    }

    public function testGetRouters()
    {
        $routers = $this->_model->getRouters();
        $routerIds = array_keys($routers);
        foreach (array('admin', 'standard', 'default', 'cms', 'vde') as $routerId) {
            $this->assertContains($routerId, $routerIds);
            $this->assertInstanceOf('Magento\Core\Controller\Varien\Router\AbstractRouter', $routers[$routerId]);
        }
    }

    public function testDispatch()
    {
        if (!\Magento\TestFramework\Helper\Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Cant\'t test dispatch process without sending headers');
        }
        $_SERVER['HTTP_HOST'] = 'localhost';
        /* empty action */
        $this->_model->getRequest()->setRequestUri('core/index/index');
        $this->_model->dispatch();
        $this->assertEmpty($this->_model->getResponse()->getBody());
    }

    /**
     * @param string $sourcePath
     * @param string $resultPath
     *
     * @dataProvider applyRewritesDataProvider
     * @magentoConfigFixture global/rewrite/test_url/from /test\/(\w*)/
     * @magentoConfigFixture global/rewrite/test_url/to   new_test/$1/subdirectory
     * @magentoDataFixture Magento/Core/_files/url_rewrite.php
     * @magentoDbIsolation enabled
     */
    public function testApplyRewrites($sourcePath, $resultPath)
    {
        /** @var $request \Magento\Core\Controller\Request\Http */
        $request = $this->_objectManager->create('Magento\Core\Controller\Request\Http');
        $request->setPathInfo($sourcePath);

        $this->_model->applyRewrites($request);
        $this->assertEquals($resultPath, $request->getPathInfo());
    }

    /**
     * Data provider for testApplyRewrites
     *
     * @return array
     */
    public function applyRewritesDataProvider()
    {
        return array(
            'url rewrite' => array(
                '$sourcePath' => '/test_rewrite_path',      // data from fixture
                '$resultPath' => 'cms/page/view/page_id/1', // data from fixture
            ),
            'configuration rewrite' => array(
                '$sourcePath' => '/test/url/',
                '$resultPath' => '/new_test/url/subdirectory/',
            ),
        );
    }
}
