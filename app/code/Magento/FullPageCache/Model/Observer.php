<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Full page cache observer
 *
 * @category   Magento
 * @package    Magento_FullPageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\FullPageCache\Model;

class Observer
{
    /*
     * Design exception key
     */
    const XML_PATH_DESIGN_EXCEPTION = 'design/package/ua_regexp';

    /**
     * Page Cache Processor
     *
     * @var \Magento\FullPageCache\Model\Processor
     */
    protected $_processor;

    /**
     * Page Cache Config
     *
     * @var \Magento\FullPageCache\Model\Config
     */
    protected $_config;

    /**
     * Is Enabled Full Page Cache
     *
     * @var bool
     */
    protected $_isEnabled;

    /**
     * @var \Magento\Core\Model\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\FullPageCache\Model\Cookie
     */
    protected $_cookie;

    /**
     * FPC cache model
     *
     * @var \Magento\FullPageCache\Model\Cache
     */
    protected $_fpcCache;

    /**
     * FPC processor restriction model
     *
     * @var \Magento\FullPageCache\Model\Processor\RestrictionInterface
     */
    protected $_restriction;

    /**
     * Request identifier model
     *
     * @var \Magento\FullPageCache\Model\Request\Identifier
     */
    protected $_requestIdentifier;

    /**
     * Design rules
     *
     * @var \Magento\FullPageCache\Model\DesignPackage\Rules
     */
    protected $_designRules;

    /**
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @param \Magento\FullPageCache\Model\Request\Identifier $_requestIdentifier
     * @param \Magento\FullPageCache\Model\Config $config
     * @param \Magento\Core\Model\Cache\StateInterface $cacheState
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\Cookie $cookie
     * @param \Magento\FullPageCache\Model\Processor\RestrictionInterface $restriction
     * @param \Magento\FullPageCache\Model\DesignPackage\Rules $designRules
     */
    public function __construct(
        \Magento\FullPageCache\Model\Processor $processor,
        \Magento\FullPageCache\Model\Request\Identifier $_requestIdentifier,
        \Magento\FullPageCache\Model\Config $config,
        \Magento\Core\Model\Cache\StateInterface $cacheState,
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\FullPageCache\Model\Cookie $cookie,
        \Magento\FullPageCache\Model\Processor\RestrictionInterface $restriction,
        \Magento\FullPageCache\Model\DesignPackage\Rules $designRules
    ) {
        $this->_processor = $processor;
        $this->_config    = $config;
        $this->_cacheState = $cacheState;
        $this->_fpcCache = $fpcCache;
        $this->_cookie = $cookie;
        $this->_restriction = $restriction;
        $this->_requestIdentifier = $_requestIdentifier;
        $this->_designRules = $designRules;
        $this->_isEnabled = $this->_cacheState->isEnabled('full_page');
    }

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->_isEnabled;
    }

    /**
     * Save page body to cache storage
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function cacheResponse(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $frontController = $observer->getEvent()->getFront();
        $request = $frontController->getRequest();
        $response = $frontController->getResponse();
        $this->_saveDesignException();
        $this->_processor->processRequestResponse($request, $response);
        return $this;
    }

    /**
     * Check when cache should be disabled
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function processPreDispatch(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $action = $observer->getEvent()->getControllerAction();
        /* @var $request \Magento\Core\Controller\Request\Http */
        $request = $action->getRequest();
        /**
         * Check if request will be cached
         */
        if ($this->_processor->canProcessRequest($request) && $this->_processor->getRequestProcessor($request)) {
            $this->_cacheState->setEnabled(\Magento\Core\Block\AbstractBlock::CACHE_GROUP, false); // disable blocks cache
            \Mage::getSingleton('Magento\Catalog\Model\Session')->setParamsMemorizeDisabled(true);
        } else {
            \Mage::getSingleton('Magento\Catalog\Model\Session')->setParamsMemorizeDisabled(false);
        }
        $this->_cookie->updateCustomerCookies();
        return $this;
    }

    /**
     * Checks whether exists design exception value in cache.
     * If not, gets it from config and puts into cache
     *
     * @return \Magento\FullPageCache\Model\Observer
     */
    protected function _saveDesignException()
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $cacheId = \Magento\FullPageCache\Model\DesignPackage\Info::DESIGN_EXCEPTION_KEY;
        $exception = $this->_fpcCache->load($cacheId);
        if (!$exception) {
            $exception = \Mage::getStoreConfig(self::XML_PATH_DESIGN_EXCEPTION);
            $this->_fpcCache->save($exception, $cacheId);
            $this->_requestIdentifier->refreshRequestIds();
        }
        return $this;
    }

    /**
     * model_load_after event processor. Collect tags of all loaded entities
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerModelTag(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        if ($object && $object->getId()) {
            $tags = $object->getCacheIdTags();
            if ($tags) {
                $this->_processor->addRequestTag($tags);
            }
        }
        return $this;
    }

    /**
     * Check category state on post dispatch to allow category page be cached
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function checkCategoryState(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $category = \Mage::registry('current_category');
        /**
         * Categories with category event can't be cached
         */
        if ($category && $category->getEvent()) {
            $this->_restriction->setIsDenied();
        }
        return $this;
    }

    /**
     * Check product state on post dispatch to allow product page be cached
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function checkProductState(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $product = \Mage::registry('current_product');
        /**
         * Categories with category event can't be cached
         */
        if ($product && $product->getEvent()) {
            $this->_restriction->setIsDenied();
        }
        return $this;
    }

    /**
     * Check if data changes duering object save affect cached pages
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function validateDataChanges(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        $object = \Mage::getModel('Magento\FullPageCache\Model\Validator')->checkDataChange($object);
        return $this;
    }

    /**
     * Check if data delete affect cached pages
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function validateDataDelete(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $object = $observer->getEvent()->getObject();
        $object = \Mage::getModel('Magento\FullPageCache\Model\Validator')->checkDataDelete($object);
        return $this;
    }

    /**
     * Clean full page cache
     *
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function cleanCache()
    {
        $this->_fpcCache->clean(\Magento\FullPageCache\Model\Processor::CACHE_TAG);
        return $this;
    }

    /**
     * Invalidate full page cache
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function invalidateCache()
    {
        /** @var \Magento\Core\Model\Cache\TypeListInterface $cacheTypeList */
        $cacheTypeList = \Mage::getObjectManager()->get('Magento\Core\Model\Cache\TypeListInterface');
        $cacheTypeList->invalidate('full_page');
        return $this;
    }

    /**
     * Render placeholder tags around the block if needed
     *
     * Event: core_layout_render_element
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function renderBlockPlaceholder(\Magento\Event\Observer $observer)
    {
        if (!$this->_isEnabled) {
            return $this;
        }
        $event = $observer->getEvent();
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $event->getData('layout');
        $name = $event->getData('element_name');
        if (!$layout->isBlock($name)) {
            return $this;
        }
        $block = $layout->getBlock($name);
        $transport = $event->getData('transport');
        $placeholder = $this->_config->getBlockPlaceholder($block);
        if ($transport && $placeholder && !$block->getSkipRenderTag()) {
            $blockHtml = $transport->getData('output');
            $blockHtml = $placeholder->getStartTag() . $blockHtml . $placeholder->getEndTag();
            $transport->setData('output', $blockHtml);
        }
        return $this;
    }

    /**
     * Set cart hash in cookie on quote change
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerQuoteChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var \Magento\Sales\Model\Quote */
        $quote = ($observer->getEvent()->getQuote()) ? $observer->getEvent()->getQuote() :
            $observer->getEvent()->getQuoteItem()->getQuote();
        $this->_cookie->setObscure(\Magento\FullPageCache\Model\Cookie::COOKIE_CART, 'quote_' . $quote->getId());

        $cacheId = \Magento\FullPageCache\Model\Container\Advanced\Quote::getCacheId();
        $this->_fpcCache->remove($cacheId);

        return $this;
    }

    /**
     * Set compare list in cookie on list change. Also modify recently compared cookie.
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerCompareListChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $listItems = \Mage::helper('Magento\Catalog\Helper\Product\Compare')->getItemCollection();
        $previousList = $this->_cookie->get(\Magento\FullPageCache\Model\Cookie::COOKIE_COMPARE_LIST);
        $previousList = (empty($previousList)) ? array() : explode(',', $previousList);

        $ids = array();
        foreach ($listItems as $item) {
            $ids[] = $item->getId();
        }
        sort($ids);
        $this->_cookie->set(\Magento\FullPageCache\Model\Cookie::COOKIE_COMPARE_LIST, implode(',', $ids));

        //Recenlty compared products processing
        $recentlyComparedProducts = $this->_cookie
            ->get(\Magento\FullPageCache\Model\Cookie::COOKIE_RECENTLY_COMPARED);
        $recentlyComparedProducts = (empty($recentlyComparedProducts)) ? array()
            : explode(',', $recentlyComparedProducts);

        //Adding products deleted from compare list to "recently compared products"
        $deletedProducts = array_diff($previousList, $ids);
        $recentlyComparedProducts = array_merge($recentlyComparedProducts, $deletedProducts);

        //Removing products from recently product list if it's present in compare list
        $addedProducts = array_diff($ids, $previousList);
        $recentlyComparedProducts = array_diff($recentlyComparedProducts, $addedProducts);

        $recentlyComparedProducts = array_unique($recentlyComparedProducts);
        sort($recentlyComparedProducts);

        $this->_cookie->set(\Magento\FullPageCache\Model\Cookie::COOKIE_RECENTLY_COMPARED,
            implode(',', $recentlyComparedProducts));

       return $this;
    }

    /**
     * Set new message cookie on adding messsage to session.
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function processNewMessage(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->set(\Magento\FullPageCache\Model\Cookie::COOKIE_MESSAGE, '1');
        return $this;
    }


    /**
     * Update customer viewed products index and renew customer viewed product ids cookie
     *
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function updateCustomerProductIndex()
    {
        try {
            $productIds = $this->_cookie->get(\Magento\FullPageCache\Model\Container\Viewedproducts::COOKIE_NAME);
            if ($productIds) {
                $productIds = explode(',', $productIds);
                \Mage::getModel('Magento\Reports\Model\Product\Index\Viewed')->registerIds($productIds);
            }
        } catch (\Exception $e) {
            \Mage::logException($e);
        }

        // renew customer viewed product ids cookie
        $countLimit = \Mage::getStoreConfig(\Magento\Reports\Block\Product\Viewed::XML_PATH_RECENTLY_VIEWED_COUNT);
        $collection = \Mage::getResourceModel('Magento\Reports\Model\Resource\Product\Index\Viewed\Collection')
            ->addIndexFilter()
            ->setAddedAtOrder()
            ->setPageSize($countLimit)
            ->setCurPage(1)
            ->setVisibility(\Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInSiteIds());

        $productIds = $collection->load()->getLoadedIds();
        $productIds = implode(',', $productIds);
        $this->_cookie->registerViewedProducts($productIds, $countLimit, false);
        return $this;
    }

    /**
     * Set cookie for logged in customer
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function customerLogin(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->updateCustomerCookies();
        $this->updateCustomerProductIndex();
        return $this;
    }

    /**
     * Remove customer cookie
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function customerLogout(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->updateCustomerCookies();

        if (!$this->_cookie->get(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER)) {
            $this->_cookie->delete(\Magento\FullPageCache\Model\Cookie::COOKIE_RECENTLY_COMPARED);
            $this->_cookie->delete(\Magento\FullPageCache\Model\Cookie::COOKIE_COMPARE_LIST);
            \Magento\FullPageCache\Model\Cookie::registerViewedProducts(array(), 0, false);
        }

        return $this;
    }

    /**
     * Set wishlist hash in cookie on wishlist change
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerWishlistChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $cookieValue = '';
        foreach (\Mage::helper('Magento\Wishlist\Helper\Data')->getWishlistItemCollection() as $item) {
            $cookieValue .= ($cookieValue ? '_' : '') . $item->getId();
        }

        // Wishlist sidebar hash
        $this->_cookie->setObscure(\Magento\FullPageCache\Model\Cookie::COOKIE_WISHLIST, $cookieValue);

        // Wishlist items count hash for top link
        $this->_cookie->setObscure(\Magento\FullPageCache\Model\Cookie::COOKIE_WISHLIST_ITEMS,
            'wishlist_item_count_' . \Mage::helper('Magento\Wishlist\Helper\Data')->getItemCount());

        return $this;
    }

    /**
     * Clear wishlist list
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerWishlistListChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $placeholder = \Mage::getSingleton('Magento\FullPageCache\Model\Container\PlaceholderFactory')
            ->create('WISHLISTS');

        $blockContainer = \Mage::getModel(
            '\Magento\FullPageCache\Model\Container\Wishlists', array('placeholder' => $placeholder)
        );
        $this->_fpcCache->remove($blockContainer->getCacheId());

        return $this;
    }

    /**
     * Set poll hash in cookie on poll vote
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerPollChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $cookieValue = $observer->getEvent()->getPoll()->getId();
        $this->_cookie->set(\Magento\FullPageCache\Model\Cookie::COOKIE_POLL, $cookieValue);

        return $this;
    }

    /**
     * Clean order sidebar cache
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerNewOrder(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        // Customer order sidebar tag
        $cacheId = md5($this->_cookie->get(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER));
        $this->_fpcCache->remove($cacheId);
        return $this;
    }

    /**
     * Remove new message cookie on clearing session messages.
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function processMessageClearing(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->delete(\Magento\FullPageCache\Model\Cookie::COOKIE_MESSAGE);
        return $this;
    }

    /**
     * Resave exception rules to cache storage
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function registerDesignExceptionsChange(\Magento\Event\Observer $observer)
    {
        $object = $observer->getDataObject();
        $this->_fpcCache->save($object->getValue(), \Magento\FullPageCache\Model\DesignPackage\Info::DESIGN_EXCEPTION_KEY,
                array(\Magento\FullPageCache\Model\Processor::CACHE_TAG));
        return $this;
    }

    /**
     * Update info about product on product page
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function updateProductInfo(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $paramsObject = $observer->getEvent()->getParams();
        if ($paramsObject instanceof \Magento\Object) {
            if (array_key_exists(\Magento\FullPageCache\Model\Cookie::COOKIE_CATEGORY_ID, $_COOKIE)) {
                $paramsObject->setCategoryId($_COOKIE[\Magento\FullPageCache\Model\Cookie::COOKIE_CATEGORY_ID]);
            }
        }
        return $this;
    }

    /**
     * Check cross-domain session messages
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function checkMessages(\Magento\Event\Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();
        if (!$transport || !$transport->getUrl()) {
            return $this;
        }
        $url = $transport->getUrl();
        $httpHost = \Mage::app()->getFrontController()->getRequest()->getHttpHost();
        $urlHost = parse_url($url, PHP_URL_HOST);
        if ($httpHost != $urlHost && \Mage::getSingleton('Magento\Core\Model\Session')->getMessages()->count() > 0) {
            $transport->setUrl(\Mage::helper('Magento\Core\Helper\Url')->addRequestParam(
                $url,
                array(\Magento\FullPageCache\Model\Cache::REQUEST_MESSAGE_GET_PARAM => null)
            ));
        }
        return $this;
    }

    /**
     * Observer on changed Customer SegmentIds
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function changedCustomerSegmentIds(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return;
        }
        $segmentIds = is_array($observer->getSegmentIds()) ? $observer->getSegmentIds() : array();
        $segmentsIdsString = implode(',', $segmentIds);
        $this->_cookie->set(\Magento\FullPageCache\Model\Cookie::CUSTOMER_SEGMENT_IDS, $segmentsIdsString);
    }

    /**
     * Disabling full page caching using no-cache cookie
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function setNoCacheCookie(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->set(\Magento\FullPageCache\Model\Processor\RestrictionInterface::NO_CACHE_COOKIE, '1', 0);
        return $this;
    }

    /**
     * Activating full page cache by deleting no-cache cookie
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function deleteNoCacheCookie(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_cookie->delete(\Magento\FullPageCache\Model\Processor\RestrictionInterface::NO_CACHE_COOKIE);
        return $this;
    }

    /**
     * Invalidate design changes cache when design change was added/deleted
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\FullPageCache\Model\Observer
     */
    public function invalidateDesignChange(\Magento\Event\Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        /** @var $design \Magento\Core\Model\Design */
        $design = $observer->getEvent()->getObject();
        $cacheId = $this->_designRules->getCacheId($design->getStoreId());
        $this->_fpcCache->remove($cacheId);

        return $this;
    }
}
