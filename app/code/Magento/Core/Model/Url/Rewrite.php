<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Url rewrite model class
 *
 * @method Magento_Core_Model_Resource_Url_Rewrite _getResource()
 * @method Magento_Core_Model_Resource_Url_Rewrite getResource()
 * @method Magento_Core_Model_Url_Rewrite setStoreId(int $value)
 * @method int getCategoryId()
 * @method Magento_Core_Model_Url_Rewrite setCategoryId(int $value)
 * @method int getProductId()
 * @method Magento_Core_Model_Url_Rewrite setProductId(int $value)
 * @method string getIdPath()
 * @method Magento_Core_Model_Url_Rewrite setIdPath(string $value)
 * @method string getRequestPath()
 * @method Magento_Core_Model_Url_Rewrite setRequestPath(string $value)
 * @method string getTargetPath()
 * @method Magento_Core_Model_Url_Rewrite setTargetPath(string $value)
 * @method int getIsSystem()
 * @method Magento_Core_Model_Url_Rewrite setIsSystem(int $value)
 * @method string getOptions()
 * @method Magento_Core_Model_Url_Rewrite setOptions(string $value)
 * @method string getDescription()
 * @method Magento_Core_Model_Url_Rewrite setDescription(string $value)
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Url_Rewrite extends Magento_Core_Model_Abstract
{
    const TYPE_CATEGORY = 1;
    const TYPE_PRODUCT  = 2;
    const TYPE_CUSTOM   = 3;
    const REWRITE_REQUEST_PATH_ALIAS = 'rewrite_request_path';

    /**
     * Cache tag for clear cache in after save and after delete
     *
     * @var mixed | array | string | boolean
     */
    protected $_cacheTag = false;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Resource_Url_Rewrite');
    }

    /**
     * Clean cache for front-end menu
     *
     * @return  Magento_Core_Model_Url_Rewrite
     */
    protected function _afterSave()
    {
        if ($this->hasCategoryId()) {
            $this->_cacheTag = array(Magento_Catalog_Model_Category::CACHE_TAG, Magento_Core_Model_Store_Group::CACHE_TAG);
        }

        parent::_afterSave();

        return $this;
    }

    /**
     * Load rewrite information for request
     * If $path is array - we must load possible records and choose one matching earlier record in array
     *
     * @param   mixed $path
     * @return  Magento_Core_Model_Url_Rewrite
     */
    public function loadByRequestPath($path)
    {
        $this->setId(null);
        $this->_getResource()->loadByRequestPath($this, $path);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    public function loadByIdPath($path)
    {
        $this->setId(null)->load($path, 'id_path');
        return $this;
    }

    public function loadByTags($tags)
    {
        $this->setId(null);

        $loadTags = is_array($tags) ? $tags : explode(',', $tags);

        $search = $this->getResourceCollection();
        foreach ($loadTags as $k => $t) {
            if (!is_numeric($k)) {
                $t = $k . '=' . $t;
            }
            $search->addTagsFilter($t);
        }
        if (!is_null($this->getStoreId())) {
            $search->addStoreFilter($this->getStoreId());
        }

        $search->setPageSize(1)->load();

        if ($search->getSize() > 0) {
            foreach ($search as $rewrite) {
                $this->setData($rewrite->getData());
            }
        }

        return $this;
    }

    public function hasOption($key)
    {
        $optArr = explode(',', $this->getOptions());

        return array_search($key, $optArr) !== false;
    }

    public function addTag($tags)
    {
        $curTags = $this->getTags();

        $addTags = is_array($tags) ? $tags : explode(',', $tags);

        foreach ($addTags as $k => $t) {
            if (!is_numeric($k)) {
                $t = $k . '=' . $t;
            }
            if (!in_array($t, $curTags)) {
                $curTags[] = $t;
            }
        }

        $this->setTags($curTags);

        return $this;
    }

    public function removeTag($tags)
    {
        $curTags = $this->getTags();

        $removeTags = is_array($tags) ? $tags : explode(',', $tags);

        foreach ($removeTags as $t) {
            if (!is_numeric($k)) {
                $t = $k.'='.$t;
            }

            $key = array_search($t, $curTags);
            if ($key) {
                unset($curTags[$key]);
            }
        }

        $this->setTags(',', $curTags);

        return $this;
    }

    /**
     * Implement logic of custom rewrites
     *
     * @param   Zend_Controller_Request_Http $request
     * @param   Zend_Controller_Response_Http $response
     * @return  Magento_Core_Model_Url
     */
    public function rewrite(Zend_Controller_Request_Http $request=null, Zend_Controller_Response_Http $response=null)
    {
        if (!Mage::isInstalled()) {
            return false;
        }
        if (is_null($request)) {
            $request = Mage::app()->getFrontController()->getRequest();
        }
        if (is_null($response)) {
            $response = Mage::app()->getFrontController()->getResponse();
        }
        if (is_null($this->getStoreId()) || false === $this->getStoreId()) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        /**
         * We have two cases of incoming paths - with and without slashes at the end ("/somepath/" and "/somepath").
         * Each of them matches two url rewrite request paths - with and without slashes at the end ("/somepath/" and "/somepath").
         * Choose any matched rewrite, but in priority order that depends on same presence of slash and query params.
         */
        $requestCases = array();
        $pathInfo = $request->getPathInfo();
        $origSlash = (substr($pathInfo, -1) == '/') ? '/' : '';
        $requestPath = trim($pathInfo, '/');

        // If there were final slash - add nothing to less priority paths. And vice versa.
        $altSlash = $origSlash ? '' : '/';

        $queryString = $this->_getQueryString(); // Query params in request, matching "path + query" has more priority
        if ($queryString) {
            $requestCases[] = $requestPath . $origSlash . '?' . $queryString;
            $requestCases[] = $requestPath . $altSlash . '?' . $queryString;
        }
        $requestCases[] = $requestPath . $origSlash;
        $requestCases[] = $requestPath . $altSlash;

        $this->loadByRequestPath($requestCases);

        /**
         * Try to find rewrite by request path at first, if no luck - try to find by id_path
         */
        if (!$this->getId() && isset($_GET['___from_store'])) {
            try {
                $fromStoreId = Mage::app()->getStore($_GET['___from_store'])->getId();
            } catch (Exception $e) {
                return false;
            }

            $this->setStoreId($fromStoreId)->loadByRequestPath($requestCases);
            if (!$this->getId()) {
                return false;
            }
            $currentStore = Mage::app()->getStore();
            $this->setStoreId($currentStore->getId())->loadByIdPath($this->getIdPath());

            Mage::app()->getCookie()->set(Magento_Core_Model_Store::COOKIE_NAME, $currentStore->getCode(), true);
            $targetUrl = $request->getBaseUrl(). '/' . $this->getRequestPath();

            $this->_sendRedirectHeaders($targetUrl, true);
        }

        if (!$this->getId()) {
            return false;
        }


        $request->setAlias(self::REWRITE_REQUEST_PATH_ALIAS, $this->getRequestPath());
        $external = substr($this->getTargetPath(), 0, 6);
        $isPermanentRedirectOption = $this->hasOption('RP');
        if ($external === 'http:/' || $external === 'https:') {
            $destinationStoreCode = Mage::app()->getStore($this->getStoreId())->getCode();
            Mage::app()->getCookie()->set(Magento_Core_Model_Store::COOKIE_NAME, $destinationStoreCode, true);

            $this->_sendRedirectHeaders($this->getTargetPath(), $isPermanentRedirectOption);
        } else {
            $targetUrl = $request->getBaseUrl(). '/' . $this->getTargetPath();
        }
        $isRedirectOption = $this->hasOption('R');
        if ($isRedirectOption || $isPermanentRedirectOption) {
            if ($this->_coreStoreConfig->getConfig('web/url/use_store') && $storeCode = Mage::app()->getStore()->getCode()) {
                $targetUrl = $request->getBaseUrl(). '/' . $storeCode . '/' .$this->getTargetPath();
            }

            $this->_sendRedirectHeaders($targetUrl, $isPermanentRedirectOption);
        }

        if ($this->_coreStoreConfig->getConfig('web/url/use_store') && $storeCode = Mage::app()->getStore()->getCode()) {
                $targetUrl = $request->getBaseUrl(). '/' . $storeCode . '/' .$this->getTargetPath();
        }

        $queryString = $this->_getQueryString();
        if ($queryString) {
            $targetUrl .= '?'.$queryString;
        }

        $request->setRequestUri($targetUrl);
        $request->setPathInfo($this->getTargetPath());

        return true;
    }

    protected function _getQueryString()
    {
        if (!empty($_SERVER['QUERY_STRING'])) {
            $queryParams = array();
            parse_str($_SERVER['QUERY_STRING'], $queryParams);
            $hasChanges = false;
            foreach ($queryParams as $key => $value) {
                if (substr($key, 0, 3) === '___') {
                    unset($queryParams[$key]);
                    $hasChanges = true;
                }
            }
            if ($hasChanges) {
                return http_build_query($queryParams);
            } else {
                return $_SERVER['QUERY_STRING'];
            }
        }
        return false;
    }

    public function getStoreId()
    {
        return $this->_getData('store_id');
    }

    /**
     * Add location header and disable browser page caching
     *
     * @param string $url
     * @param bool $isPermanent
     */
    protected function _sendRedirectHeaders($url, $isPermanent = false)
    {
        if ($isPermanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Location: ' . $url);
        exit;
    }
}
