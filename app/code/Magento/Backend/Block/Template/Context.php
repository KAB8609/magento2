<?php

namespace Magento\Backend\Block\Template;

/**
 * Backend block template context
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context extends \Magento\Core\Block\Template\Context
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\View\Layout $layout
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\UrlInterface $urlBuilder
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\View\Design $design
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Controller\Varien\Front $frontController
     * @param \Magento\Core\Model\Factory\Helper $helperFactory
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\View\Config $viewConfig
     * @param \Magento\Core\Model\Cache\StateInterface $cacheState
     * @param \Magento\Core\Model\Dir $dirs
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\View\FileSystem $viewFileSystem
     * @param \Magento\Core\Model\TemplateEngine\Factory $engineFactory
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Core\Model\LocaleInterface $locale
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Controller\Request\Http $request,
        \Magento\View\Layout $layout,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\UrlInterface $urlBuilder,
        \Magento\Core\Model\Translate $translator,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\View\Design $design,
        \Magento\Core\Model\Session $session,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Controller\Varien\Front $frontController,
        \Magento\Core\Model\Factory\Helper $helperFactory,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\View\Config $viewConfig,
        \Magento\Core\Model\Cache\StateInterface $cacheState,
        \Magento\Core\Model\Dir $dirs,
        \Magento\Core\Model\Logger $logger,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\View\FileSystem $viewFileSystem,
        \Magento\Core\Model\TemplateEngine\Factory $engineFactory,
        \Magento\AuthorizationInterface $authorization,
        \Magento\Core\Model\App $app,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Core\Model\LocaleInterface $locale
    ) {
        parent::__construct(
            $request, $layout, $eventManager, $urlBuilder, $translator, $cache, $design, $session, $storeConfig,
            $frontController, $helperFactory, $viewUrl, $viewConfig, $cacheState,
            $dirs, $logger, $filesystem, $viewFileSystem, $engineFactory, $app
        );
        $this->_storeManager = $storeManager;
        $this->_authorization = $authorization;
        $this->_backendSession = $backendSession;
        $this->_locale = $locale;
    }

    /**
     * Get store manager
     *
     * @return \Magento\Core\Model\StoreManager
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * Retrieve Authorization
     *
     * @return \\Magento\AuthorizationInterface
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }

    /**
     * @return \Magento\Backend\Model\Session
     */
    public function getBackendSession()
    {
        return $this->_backendSession;
    }

    /**
     * @return \Magento\Core\Model\LocaleInterface
     */
    public function getLocale()
    {
        return $this->_locale;
    }
}
