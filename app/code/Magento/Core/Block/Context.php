<?php
/**
 * Abstract block context object. Will be used as block constructor modification point after release.
 * Important: Should not be modified by extension developers.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Block;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * @var \Magento\Core\Model\CacheInterface
     */
    protected $_cache;

    /**
     * @var \Magento\Core\Model\View\DesignInterface
     */
    protected $_design;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Core\Controller\Varien\Front
     */
    protected $_frontController;

    /**
     * @var \Magento\Core\Model\Factory\Helper
     */
    protected $_helperFactory;

    /**
     * @var \Magento\Core\Model\View\Url
     */
    protected $_viewUrl;

    /**
     * View config model
     *
     * @var \Magento\Core\Model\View\Config
     */
    protected $_viewConfig;

    /**
     * @var \Magento\Core\Model\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\UrlInterface $urlBuilder
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\View\DesignInterface $design
     * @param \Magento\Core\Model\Session\AbstractSession $session
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Controller\Varien\Front $frontController
     * @param \Magento\Core\Model\Factory\Helper $helperFactory
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Core\Model\View\Config $viewConfig
     * @param \Magento\Core\Model\Cache\StateInterface $cacheState
     * @param Magento_Core_Model_Logger $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Core\Model\Layout $layout,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\UrlInterface $urlBuilder,
        \Magento\Core\Model\Translate $translator,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\View\DesignInterface $design,
        \Magento\Core\Model\Session\AbstractSession $session,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Controller\Varien\Front $frontController,
        \Magento\Core\Model\Factory\Helper $helperFactory,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Core\Model\View\Config $viewConfig,
        \Magento\Core\Model\Cache\StateInterface $cacheState,
        Magento_Core_Model_Logger $logger,
        array $data = array()
    ) {
        $this->_request         = $request;
        $this->_layout          = $layout;
        $this->_eventManager    = $eventManager;
        $this->_urlBuilder      = $urlBuilder;
        $this->_translator      = $translator;
        $this->_cache           = $cache;
        $this->_design          = $design;
        $this->_session         = $session;
        $this->_storeConfig     = $storeConfig;
        $this->_frontController = $frontController;
        $this->_helperFactory   = $helperFactory;
        $this->_viewUrl         = $viewUrl;
        $this->_viewConfig      = $viewConfig;
        $this->_cacheState      = $cacheState;
        $this->_logger          = $logger;
    }

    /**
     * @return \Magento\Core\Model\CacheInterface
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * @return \Magento\Core\Model\View\DesignInterface
     */
    public function getDesignPackage()
    {
        return $this->_design;
    }

    /**
     * @return \Magento\Core\Model\Event\Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Core\Controller\Varien\Front
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }

    /**
     * @return \Magento\Core\Model\Factory\Helper
     */
    public function getHelperFactory()
    {
        return $this->_helperFactory;
    }

    /**
     * @return \Magento\Core\Model\Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return \Magento\Core\Controller\Request\Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return \Magento\Core\Model\Session|\Magento\Core\Model\Session\AbstractSession
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * @return \Magento\Core\Model\Store\Config
     */
    public function getStoreConfig()
    {
        return $this->_storeConfig;
    }

    /**
     * @return \Magento\Core\Model\Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * @return \Magento\Core\Model\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * @return \Magento\Core\Model\View\Url
     */
    public function getViewUrl()
    {
        return $this->_viewUrl;
    }

    /**
     * @return \Magento\Core\Model\View\Config
     */
    public function getViewConfig()
    {
        return $this->_viewConfig;
    }

    /**
     * @return \Magento\Core\Model\Cache\StateInterface
     */
    public function getCacheState()
    {
        return $this->_cacheState;
    }

    /**
     * @return \Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
