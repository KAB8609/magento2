<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Controller\Varien\Action;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Core\Controller\Response\Http
     */
    protected $_response;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Controller\Varien\Front
     */
    protected $_frontController = null;

    /**
     * @var \Magento\View\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * Should inherited page be rendered
     *
     * @var bool
     */
    protected $_isRenderInherited;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Core\Controller\Response\Http $response
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Controller\Varien\Front $frontController
     * @param \Magento\View\Layout $layout
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param $isRenderInherited
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Core\Controller\Response\Http $response,
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Controller\Varien\Front $frontController,
        \Magento\View\Layout $layout,
        \Magento\Core\Model\Event\Manager $eventManager,
        $isRenderInherited
    ) {
        $this->_request           = $request;
        $this->_response          = $response;
        $this->_objectManager     = $objectManager;
        $this->_frontController   = $frontController;
        $this->_layout            = $layout;
        $this->_eventManager      = $eventManager;
        $this->_isRenderInherited = $isRenderInherited;
        $this->_logger            = $logger;
    }

    /**
     * Should inherited page be rendered
     *
     * @return bool
     */
    public function isRenderInherited()
    {
        return $this->_isRenderInherited;
    }

    /**
     * @return \Magento\Core\Controller\Varien\Front
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }

    /**
     * @return \Magento\View\Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return \Magento\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * @return \Magento\Core\Controller\Request\Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return \Magento\Core\Controller\Response\Http
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return \Magento\Core\Model\Event\Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Core\Model\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
