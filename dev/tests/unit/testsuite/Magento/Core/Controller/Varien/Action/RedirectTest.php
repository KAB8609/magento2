<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class \Magento\Core\Controller\Varien\Action\Redirect
 */
namespace Magento\Core\Controller\Varien\Action;

class RedirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Action\Redirect
     */
    protected $_object = null;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\App\Response\Http
     */
    protected $_response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_routerListMock;

    protected function setUp()
    {
        $this->_routerListMock = $this->getMock('Magento\App\Route\ConfigInterface', array(), array(), '', false);
        $this->_request  = new \Magento\App\Request\Http($this->_routerListMock);
        $this->_response = new \Magento\App\Response\Http();

        $this->_object = new \Magento\App\Action\Redirect($this->_request, $this->_response);
    }

    protected function tearDown()
    {
        unset($this->_object);
        unset($this->_request);
        unset($this->_response);
    }

    public function testDispatch()
    {
        $this->_request->setDispatched(true);
        $this->assertTrue($this->_request->isDispatched());
        $this->_object->dispatch('any action');
        $this->assertTrue($this->_request->isDispatched());

        $this->_request->setDispatched(false);
        $this->assertFalse($this->_request->isDispatched());
        $this->_object->dispatch('any action');
        $this->assertFalse($this->_request->isDispatched());
    }
}
