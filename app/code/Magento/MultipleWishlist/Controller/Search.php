<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multiple wishlist frontend search controller
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_MultipleWishlist_Controller_Search extends Magento_Core_Controller_Front_Action
{
    /**
     * Localization filter
     *
     * @var Zend_Filter_LocalizedToNormalized
     */
    protected $_localFilter;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Locale model
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Checkout cart
     *
     * @var Magento_Checkout_Model_Cart
     */
    protected $_checkoutCart;

    /**
     * Checkout session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * Strategy name factory
     *
     * @var Magento_MultipleWishlist_Model_Search_Strategy_NameFactory
     */
    protected $_strategyNameFactory;

    /**
     * Strategy email factory
     *
     * @var Magento_MultipleWishlist_Model_Search_Strategy_EmailFactory
     */
    protected $_strategyEmailFactory;

    /**
     * Search factory
     *
     * @var Magento_MultipleWishlist_Model_SearchFactory
     */
    protected $_searchFactory;

    /**
     * Wishlist factory
     *
     * @var Magento_Wishlist_Model_WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * Item model factory
     *
     * @var Magento_Wishlist_Model_ItemFactory
     */
    protected $_itemFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Wishlist_Model_ItemFactory $itemFactory
     * @param Magento_Wishlist_Model_WishlistFactory $wishlistFactory
     * @param Magento_MultipleWishlist_Model_SearchFactory $searchFactory
     * @param Magento_MultipleWishlist_Model_Search_Strategy_EmailFactory $strategyEmailFactory
     * @param Magento_MultipleWishlist_Model_Search_Strategy_NameFactory $strategyNameFactory
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Checkout_Model_Cart $checkoutCart
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_LocaleInterface $locale
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Wishlist_Model_ItemFactory $itemFactory,
        Magento_Wishlist_Model_WishlistFactory $wishlistFactory,
        Magento_MultipleWishlist_Model_SearchFactory $searchFactory,
        Magento_MultipleWishlist_Model_Search_Strategy_EmailFactory $strategyEmailFactory,
        Magento_MultipleWishlist_Model_Search_Strategy_NameFactory $strategyNameFactory,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Checkout_Model_Cart $checkoutCart,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_LocaleInterface $locale
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_itemFactory = $itemFactory;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_searchFactory = $searchFactory;
        $this->_strategyEmailFactory = $strategyEmailFactory;
        $this->_strategyNameFactory = $strategyNameFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_checkoutCart = $checkoutCart;
        $this->_customerSession = $customerSession;
        $this->_locale = $locale;
        parent::__construct($context);
    }

    /**
     * Processes localized qty (entered by user at frontend) into internal php format
     *
     * @param string $qty
     * @return float|int|null
     */
    protected function _processLocalizedQty($qty)
    {
        if (!$this->_localFilter) {
            $this->_localFilter = new Zend_Filter_LocalizedToNormalized(
                array('locale' => $this->_locale->getLocaleCode())
            );
        }
        $qty = $this->_localFilter->filter($qty);
        if ($qty < 0) {
            $qty = null;
        }
        return $qty;
    }

    /**
     * Check if multiple wishlist is enabled on current store before all other actions
     *
     * @return Magento_MultipleWishlist_Controller_Search
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Magento_MultipleWishlist_Helper_Data')->isModuleEnabled()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Wish List Search'));
        }
        $this->renderLayout();
    }

    /**
     * Wishlist search action
     *
     * @throws Magento_Core_Exception
     */
    public function resultsAction()
    {
        $this->loadLayout();

        try {
            $params = $this->getRequest()->getParam('params');
            if (empty($params) || !is_array($params) || empty($params['search'])) {
                throw new Magento_Core_Exception(__('Please specify correct search options.'));
            };

            $strategy = null;
            switch ($params['search']) {
                case 'type':
                    $strategy = $this->_strategyNameFactory->create();
                    break;
                case 'email':
                    $strategy = $this->_strategyEmailFactory->create();
                    break;
                default:
                    throw new Magento_Core_Exception(__('Please specify correct search options.'));
            }

            $strategy->setSearchParams($params);
            /** @var Magento_MultipleWishlist_Model_Search $search */
            $search = $this->_searchFactory->create();
            $this->_coreRegistry->register('search_results', $search->getResults($strategy));
            $this->_customerSession->setLastWishlistSearchParams($params);
        } catch (InvalidArgumentException $e) {
            $this->_customerSession->addNotice($e->getMessage());
        } catch (Magento_Core_Exception $e) {
            $this->_customerSession->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_customerSession->addError(__('We could not perform the search.'));
        }

        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Wish List Search'));
        }
        $this->renderLayout();
    }

    /**
     * View customer wishlist
     */
    public function viewAction()
    {
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        if (!$wishlistId) {
            return $this->norouteAction();
        }
        /** @var Magento_Wishlist_Model_Wishlist $wishlist */
        $wishlist = $this->_wishlistFactory->create();
        $wishlist->load($wishlistId);
        if (!$wishlist->getId()
            || (!$wishlist->getVisibility() && $wishlist->getCustomerId != $this->_customerSession->getCustomerId())) {
            return $this->norouteAction();
        }
        $this->_coreRegistry->register('wishlist', $wishlist);
        $this->loadLayout();
        $block = $this->getLayout()->getBlock('customer.wishlist.info');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->_initLayoutMessages(array('Magento_Customer_Model_Session', 'Magento_Checkout_Model_Session', 'Magento_Wishlist_Model_Session'));
        $this->renderLayout();
    }

    /**
     * Add wishlist item to cart
     */
    public function addtocartAction()
    {
        $messages   = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();

        /** @var Magento_Checkout_Model_Cart $cart  */
        $cart = $this->_checkoutCart;
        $qtys = $this->getRequest()->getParam('qty');
        $selected = $this->getRequest()->getParam('selected');
        foreach ($qtys as $itemId => $qty) {
            if ($qty && isset($selected[$itemId])) {
                try {
                    /** @var Magento_Wishlist_Model_Item $item*/
                    $item = $this->_itemFactory->create();
                    $item->loadWithOptions($itemId);
                    $item->unsProduct();
                    $qty = $this->_processLocalizedQty($qty);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                    if ($item->addToCart($cart, false)) {
                        $addedItems[] = $item->getProduct();
                    }
                } catch (Magento_Core_Exception $e) {
                    if ($e->getCode() == Magento_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                        $notSalable[] = $item;
                    } else if ($e->getCode() == Magento_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                        $hasOptions[] = $item;
                    } else {
                        $messages[] = __('%1 for "%2"', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                    }
                } catch (Exception $e) {
                    $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
                    $messages[] = __('We could not add the item to shopping cart.');
                }
            }
        }

        if ($this->_objectManager->get('Magento_Checkout_Helper_Cart')->getShouldRedirectToCart()) {
            $redirectUrl = $this->_objectManager->get('Magento_Checkout_Helper_Cart')->getCartUrl();
        } else if ($this->_getRefererUrl()) {
            $redirectUrl = $this->_getRefererUrl();
        }

        if ($notSalable) {
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = __('Cannot add the following product(s) to shopping cart: %1.', join(', ', $products));
        }

        if ($hasOptions) {
            $products = array();
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = __('Product(s) %1 have required options. Each product can only be added individually.', join(', ', $products));
        }

        if ($messages) {
            if ((count($messages) == 1) && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                $redirectUrl = $item->getProductUrl();
            } else {
                foreach ($messages as $message) {
                    $this->_checkoutSession->addError($message);
                }
            }
        }

        if ($addedItems) {
            $products = array();
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }

            $this->_checkoutSession->addSuccess(
                __('%1 product(s) have been added to shopping cart: %2.', count($addedItems), join(', ', $products))
            );
        }

        // save cart and collect totals
        $cart->save()->getQuote()->collectTotals();

        $this->_redirectUrl($redirectUrl);
    }
}
