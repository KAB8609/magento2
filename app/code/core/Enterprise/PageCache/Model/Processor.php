<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_Processor implements Enterprise_PageCache_Model_RequestProcessorInterface
{
    const XML_NODE_ALLOWED_CACHE        = 'frontend/cache/requests';
    const XML_PATH_ALLOWED_DEPTH        = 'system/page_cache/allowed_depth';
    const XML_PATH_CACHE_MULTICURRENCY  = 'system/page_cache/multicurrency';
    const XML_PATH_CACHE_DEBUG          = 'system/page_cache/debug';
    const CACHE_TAG                     = 'FPC';  // Full Page Cache, minimize

    const DESIGN_CHANGE_CACHE_SUFFIX    = 'FPC_DESIGN_CHANGE_CACHE';
    const CACHE_SIZE_KEY                = 'FPC_CACHE_SIZE_CAHCE_KEY';
    const XML_PATH_CACHE_MAX_SIZE       = 'system/page_cache/max_cache_size';

    /**
     * Cache tags related with request
     * @var array
     */
    protected $_requestTags;

    /**
     * Request processor model
     * @var mixed
     */
    protected $_requestProcessor = null;

    /**
     * subProcessor model
     *
     * @var Enterprise_PageCache_Model_Cache_SubProcessorInterface
     */
    protected $_subProcessor;

    /**
     * Page cache processor restriction model
     *
     * @var Enterprise_PageCache_Model_Processor_RestrictionInterface
     */
    protected $_restriction;

    /**
     * Design package model
     *
     * @var Mage_Core_Model_Design_PackageInterface
     */
    protected $_designPackage;

    /**
     * SubProcessor factory
     *
     * @var Enterprise_PageCache_Model_Cache_SubProcessorFactory
     */
    protected $_subProcessorFactory;

    /**
     * Placeholder factory
     *
     * @var Enterprise_PageCache_Model_Container_PlaceholderFactory
     */
    protected $_placeholderFactory;

    /**
     * Container factory
     *
     * @var Enterprise_PageCache_Model_ContainerFactory
     */
    protected $_containerFactory;

    /**
     * FPC cache model
     * @var Enterprise_PageCache_Model_Cache
     */
    protected $_fpcCache;

    /**
     * Application environment
     *
     * @var Enterprise_PageCache_Model_Environment
     */
    protected $_environment;

    /**
     * Request identifier model
     *
     * @var Enterprise_PageCache_Model_Request_Identifier
     */
    protected $_requestIdentifier;

    /**
     * Design info model
     *
     * @var Enterprise_PageCache_Model_DesignPackage_Info
     */
    protected $_designInfo;

    /**
     * Metadata storage model
     *
     * @var Enterprise_PageCache_Model_Metadata
     */
    protected $_metadata;

    /**
     * @param Enterprise_PageCache_Model_Processor_RestrictionInterface $restriction
     * @param Enterprise_PageCache_Model_Cache $fpcCache
     * @param Mage_Core_Model_Design_Package_Proxy $designPackage
     * @param Enterprise_PageCache_Model_Cache_SubProcessorFactory $subProcessorFactory
     * @param Enterprise_PageCache_Model_Container_PlaceholderFactory $placeholderFactory
     * @param Enterprise_PageCache_Model_ContainerFactory $containerFactory
     * @param Enterprise_PageCache_Model_Environment $environment
     * @param Enterprise_PageCache_Model_Request_Identifier $requestIdentifier
     * @param Enterprise_PageCache_Model_DesignPackage_Info $designInfo
     * @param Enterprise_PageCache_Model_Metadata $metadata
     */
    public function __construct(
        Enterprise_PageCache_Model_Processor_RestrictionInterface $restriction,
        Enterprise_PageCache_Model_Cache $fpcCache,
        Mage_Core_Model_Design_Package_Proxy $designPackage,
        Enterprise_PageCache_Model_Cache_SubProcessorFactory $subProcessorFactory,
        Enterprise_PageCache_Model_Container_PlaceholderFactory $placeholderFactory,
        Enterprise_PageCache_Model_ContainerFactory $containerFactory,
        Enterprise_PageCache_Model_Environment $environment,
        Enterprise_PageCache_Model_Request_Identifier $requestIdentifier,
        Enterprise_PageCache_Model_DesignPackage_Info $designInfo,
        Enterprise_PageCache_Model_Metadata $metadata
    ) {
        $this->_containerFactory = $containerFactory;
        $this->_placeholderFactory = $placeholderFactory;
        $this->_subProcessorFactory = $subProcessorFactory;
        $this->_designPackage = $designPackage;
        $this->_restriction = $restriction;
        $this->_fpcCache = $fpcCache;
        $this->_environment = $environment;
        $this->_designInfo = $designInfo;
        $this->_requestIdentifier = $requestIdentifier;
        $this->_metadata = $metadata;
        $this->_requestTags = array(self::CACHE_TAG);
    }


    /**
     * Get HTTP request identifier
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->_requestIdentifier->getRequestId();
    }

    /**
     * Get page identifier for loading page from cache
     *
     * @return string
     */
    public function getRequestCacheId()
    {
        return $this->_requestIdentifier->getRequestCacheId();
    }

    /**
     * Check if processor is allowed for current HTTP request.
     * Disable processing HTTPS requests and requests with "NO_CACHE" cookie
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_restriction->isAllowed($this->_requestIdentifier->getRequestId());
    }

    /**
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     * @param string $content
     * @return bool|string
     */
    public function extractContent(
        Zend_Controller_Request_Http $request,
        Zend_Controller_Response_Http $response,
        $content
    ) {

        $this->_applyDesignChange();

        if (!$this->_designInfo->isDesignExceptionExistsInCache()) {
            return false;
        }

        if (!$content && $this->isAllowed()) {
            $subProcessorClass = $this->_metadata->getMetadata('cache_subprocessor');
            if (!$subProcessorClass) {
                return $content;
            }

            /*
             * @var Enterprise_PageCache_Model_Processor_Default
             */
            $subProcessor = $this->_subProcessorFactory->create($subProcessorClass);
            $this->setSubprocessor($subProcessor);
            $cacheId = $this->_requestIdentifier->prepareCacheId($subProcessor->getPageIdWithoutApp($this));

            $content = $this->_fpcCache->load($cacheId);

            if ($content) {
                $content = $this->_processContent($this->_compressContent($content), $request);

                $this->_restoreResponseHeaders($response);

                $this->_updateRecentlyViewedProducts();
            }
        }

        return $content;
    }

    /**
     * Renew recently viewed products
     */
    protected function _updateRecentlyViewedProducts()
    {
        $productId = $this->_fpcCache->load($this->getRequestCacheId() . '_current_product_id');
        $countLimit = $this->_fpcCache->load($this->getRecentlyViewedCountCacheId());
        if ($productId && $countLimit) {
            Enterprise_PageCache_Model_Cookie::registerViewedProducts($productId, $countLimit);
        }
    }

    /**
     * Restore response headers
     *
     * @param Zend_Controller_Response_Http $response
     */
    protected function _restoreResponseHeaders(Zend_Controller_Response_Http $response)
    {
        $responseHeaders = $this->_metadata->getMetadata('response_headers');
        if (is_array($responseHeaders)) {
            foreach ($responseHeaders as $header) {
                $response->setHeader($header['name'], $header['value'], $header['replace']);
            }
        }
    }

    /**
     * Compress content if possible
     *
     * @param string $content
     * @return string
     */
    protected function _compressContent($content)
    {
        if (function_exists('gzuncompress')) {
            $content = gzuncompress($content);
            return $content;
        }
        return $content;
    }

    /**
     * Apply design change
     */
    protected function _applyDesignChange()
    {
        $designChange = $this->_fpcCache->load($this->getRequestCacheId() . self::DESIGN_CHANGE_CACHE_SUFFIX);
        if ($designChange) {
            $designChange = unserialize($designChange);
            if (is_array($designChange) && isset($designChange['design'])) {
                $this->_designPackage->setDesignTheme($designChange['design']);
            }
        }
    }

    /**
     * Retrieve recently viewed count cache identifier
     *
     * @return string
     */
    public function getRecentlyViewedCountCacheId()
    {
        $cookieName = Mage_Core_Model_Store::COOKIE_NAME;
        $additional = $this->_environment->hasCookie($cookieName) ?
            '_' . $this->_environment->getCookie($cookieName) :
            '';
        return 'recently_viewed_count' . $additional;
    }

    /**
     * Retrieve session info cache identifier
     *
     * @return string
     */
    public function getSessionInfoCacheId()
    {
        $cookieName = Mage_Core_Model_Store::COOKIE_NAME;
        $additional = $this->_environment->hasCookie($cookieName) ?
            '_' . $this->_environment->getCookie($cookieName) :
            '';
        return 'full_page_cache_session_info' . $additional;
    }

    /**
     * Determine and process all defined containers.
     * Direct request to pagecache/request/process action if necessary for additional processing
     *
     * @param string $content
     * @param Zend_Controller_Request_Http $request
     * @return string|bool
     */
    protected function _processContent($content, Zend_Controller_Request_Http $request)
    {
        $containers = $this->_processContainers($content);
        $isProcessed = empty($containers);
        // renew session cookie
        $sessionInfo = $this->_fpcCache->load($this->getSessionInfoCacheId());

        if ($sessionInfo) {
            $sessionInfo = unserialize($sessionInfo);
            foreach ($sessionInfo as $cookieName => $cookieInfo) {
                if ($this->_environment->hasCookie($cookieName) && isset($cookieInfo['lifetime'])
                    && isset($cookieInfo['path']) && isset($cookieInfo['domain'])
                    && isset($cookieInfo['secure']) && isset($cookieInfo['httponly'])
                ) {
                    $lifeTime = (0 == $cookieInfo['lifetime']) ? 0 : time() + $cookieInfo['lifetime'];
                    setcookie($cookieName, $this->_environment->getCookie($cookieName), $lifeTime,
                        $cookieInfo['path'], $cookieInfo['domain'],
                        $cookieInfo['secure'], $cookieInfo['httponly']
                    );
                }
            }
        } else {
            $isProcessed = false;
        }

        /**
         * restore session_id in content whether content is completely processed or not
         */
        $sidCookieName = $this->_metadata->getMetadata('sid_cookie_name');
        $sidCookieValue = $sidCookieName && $this->_environment->getCookie($sidCookieName, '');
        Enterprise_PageCache_Helper_Url::restoreSid($content, $sidCookieValue);

        if ($isProcessed) {
            return $content;
        } else {
            Mage::register('cached_page_content', $content);
            Mage::register('cached_page_containers', $containers);
            $request->setModuleName('pagecache')
                ->setControllerName('request')
                ->setActionName('process')
                ->isStraight(true);

            // restore original routing info
            $routingInfo = array(
                'aliases'              => $this->_metadata->getMetadata('routing_aliases'),
                'requested_route'      => $this->_metadata->getMetadata('routing_requested_route'),
                'requested_controller' => $this->_metadata->getMetadata('routing_requested_controller'),
                'requested_action'     => $this->_metadata->getMetadata('routing_requested_action')
            );

            $request->setRoutingInfo($routingInfo);
            return false;
        }
    }

    /**
     * Process Containers
     *
     * @param $content
     * @return Enterprise_PageCache_Model_ContainerInterface[]
     */
    protected function _processContainers(&$content)
    {
        $placeholders = array();
        preg_match_all(
            Enterprise_PageCache_Model_Container_Placeholder::HTML_NAME_PATTERN,
            $content, $placeholders, PREG_PATTERN_ORDER
        );
        $placeholders = array_unique($placeholders[1]);
        $containers = array();
        foreach ($placeholders as $definition) {
            $placeholder = $this->_placeholderFactory->create($definition);
            $container = $placeholder->getContainerClass();
            if (!$container) {
                continue;
            }
            $arguments = array('placeholder' => $placeholder);
            $container = $this->_containerFactory->create($container, $arguments);
            $container->setProcessor($this);
            if (!$container->applyWithoutApp($content)) {
                $containers[] = $container;
            } else {
                preg_match($placeholder->getPattern(), $content, $matches);
                if (array_key_exists(1, $matches)) {
                    $containers = array_merge($this->_processContainers($matches[1]), $containers);
                    $content = preg_replace($placeholder->getPattern(), str_replace('$', '\\$', $matches[1]), $content);
                }
            }
        }
        return $containers;
    }

    /**
     * Associate tag with page cache request identifier
     *
     * @param array|string $tag
     * @return Enterprise_PageCache_Model_Processor
     */
    public function addRequestTag($tag)
    {
        if (is_array($tag)) {
            $this->_requestTags = array_merge($this->_requestTags, $tag);
        } else {
            $this->_requestTags[] = $tag;
        }
        return $this;
    }

    /**
     * Get cache request associated tags
     * @return array
     */
    public function getRequestTags()
    {
        return $this->_requestTags;
    }

    /**
     * Process response body by specific request
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     * @return Enterprise_PageCache_Model_Processor
     */
    public function processRequestResponse(
        Zend_Controller_Request_Http $request,
        Zend_Controller_Response_Http $response
    ) {
        /**
         * Basic validation for request processing
         */
        if ($this->canProcessRequest($request)) {
            $processor = $this->getRequestProcessor($request);
            if ($processor && $processor->allowCache($request)) {
                $this->_metadata->setMetadata('cache_subprocessor', get_class($processor));

                $cacheId = $this->_requestIdentifier->prepareCacheId($processor->getPageIdInApp($this));
                $content = $processor->prepareContent($response);

                /**
                 * Replace all occurrences of session_id with unique marker
                 */
                Enterprise_PageCache_Helper_Url::replaceSid($content);

                if (function_exists('gzcompress')) {
                    $content = gzcompress($content);
                }

                $contentSize = strlen($content);
                $currentStorageSize = (int) $this->_fpcCache->load(self::CACHE_SIZE_KEY);

                $maxSizeInBytes = Mage::getStoreConfig(self::XML_PATH_CACHE_MAX_SIZE) * 1024 * 1024;

                if ($currentStorageSize >= $maxSizeInBytes) {
                    Mage::app()->getCacheInstance()->invalidateType('full_page');
                    return $this;
                }

                $this->_fpcCache->save($content, $cacheId, $this->getRequestTags());

                $this->_fpcCache->save(
                    $currentStorageSize + $contentSize,
                    self::CACHE_SIZE_KEY,
                    $this->getRequestTags()
                );

                /*
                 * Save design change in cache
                 */
                $designChange = Mage::getSingleton('Mage_Core_Model_Design');
                if ($designChange->getData()) {
                    $this->_fpcCache->save(
                        serialize($designChange->getData()),
                        $this->getRequestCacheId() . self::DESIGN_CHANGE_CACHE_SUFFIX,
                        $this->getRequestTags()
                    );
                }

                // save response headers
                $this->_metadata->setMetadata('response_headers', $response->getHeaders());

                // save original routing info
                $this->_metadata->setMetadata('routing_aliases', $request->getAliases());
                $this->_metadata->setMetadata('routing_requested_route', $request->getRequestedRouteName());
                $this->_metadata->setMetadata('routing_requested_controller', $request->getRequestedControllerName());
                $this->_metadata->setMetadata('routing_requested_action', $request->getRequestedActionName());

                $this->_metadata->setMetadata('sid_cookie_name',
                    Mage::getSingleton('Mage_Core_Model_Session')->getSessionName()
                );

                Mage::dispatchEvent('pagecache_processor_metadata_before_save', array('processor' => $this));

                $this->_metadata->saveMetadata($this->getRequestTags());
            }

            if ($this->_environment->hasQuery(Mage_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM)) {
                Mage::getSingleton('Enterprise_PageCache_Model_Cookie')->updateCustomerCookies();
                Mage::getModel('Enterprise_PageCache_Model_Observer')->updateCustomerProductIndex();
            }
        }
        return $this;
    }

    /**
     * Do basic validation for request to be cached
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function canProcessRequest(Zend_Controller_Request_Http $request)
    {
        $output = $this->isAllowed();

        if ($output) {
            $maxDepth = Mage::getStoreConfig(self::XML_PATH_ALLOWED_DEPTH);
            $queryParams = $request->getQuery();
            unset($queryParams[Enterprise_PageCache_Model_Cache::REQUEST_MESSAGE_GET_PARAM]);
            $output = count($queryParams) <= $maxDepth;
        }
        if ($output) {
            $multiCurrency = Mage::getStoreConfig(self::XML_PATH_CACHE_MULTICURRENCY);
            $currency = $this->_environment->getCookie('currency');
            if (!$multiCurrency && !empty($currency)) {
                $output = false;
            }
        }
        return $output;
    }

    /**
     * Get specific request processor based on request parameters.
     *
     * @param Zend_Controller_Request_Http $request
     * @return Enterprise_PageCache_Model_Processor_Default
     */
    public function getRequestProcessor(Zend_Controller_Request_Http $request)
    {
        if ($this->_requestProcessor === null) {
            $this->_requestProcessor = false;
            $configuration = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_CACHE);
            if ($configuration) {
                $configuration = $configuration->asArray();
            }
            $module = $request->getModuleName();
            if (isset($configuration[$module])) {
                $model = $configuration[$module];
                $controller = $request->getControllerName();
                if (is_array($configuration[$module]) && isset($configuration[$module][$controller])) {
                    $model = $configuration[$module][$controller];
                    $action = $request->getActionName();
                    if (is_array($configuration[$module][$controller])
                            && isset($configuration[$module][$controller][$action])) {
                        $model = $configuration[$module][$controller][$action];
                    }
                }
                if (is_string($model)) {
                    $this->_requestProcessor = Mage::getModel($model);
                }
            }
        }
        return $this->_requestProcessor;
    }

    /**
     * Set metadata value for specified key
     *
     * @param string $key
     * @param string $value
     *
     * @return Enterprise_PageCache_Model_Processor
     */
    public function setMetadata($key, $value)
    {
        $this->_metadata->setMetadata($key, $value);
        return $this;
    }

    /**
     * Get metadata value for specified key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getMetadata($key)
    {
        return $this->_metadata->getMetadata($key);
    }

    /**
     * Set subprocessor
     *
     * @param Enterprise_PageCache_Model_Cache_SubProcessorInterface $subProcessor
     */
    public function setSubprocessor(Enterprise_PageCache_Model_Cache_SubProcessorInterface $subProcessor)
    {
        $this->_subProcessor = $subProcessor;
    }

    /**
     * Get subprocessor
     *
     * @return Enterprise_PageCache_Model_Cache_SubProcessorInterface
     */
    public function getSubprocessor()
    {
        return $this->_subProcessor;
    }
}
