<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multiple wishlist frontend search controller
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Wishlist_Controller_Index extends Mage_Wishlist_Controller_Index
{
    /**
     * Check if multiple wishlist is enabled on current store before all other actions
     *
     * @return Enterprise_Wishlist_Controller_Index
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $action = $this->getRequest()->getActionName();
        $protectedActions = array(
            'createwishlist', 'editwishlist', 'deletewishlist', 'copyitems', 'moveitem', 'moveitems'
        );
        if (!Mage::helper('Enterprise_Wishlist_Helper_Data')->isMultipleEnabled() && in_array($action, $protectedActions)) {
            $this->norouteAction();
        }

        return $this;
    }

    /**
     * Retrieve customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Customer_Model_Session');
    }

    /**
     * Add item to wishlist
     * Create new wishlist if wishlist params (name, visibility) are provided
     */
    public function addAction()
    {
        $customerId = $this->_getSession()->getCustomerId();
        $name = $this->getRequest()->getParam('name');
        $visibility = ($this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0);
        if ($name !== null) {
            try {
                $wishlist = $this->_editWishlist($customerId, $name, $visibility);
                $this->_getSession()->addSuccess(
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Wish List "%s" was saved.', Mage::helper('Mage_Core_Helper_Data')->escapeHtml($wishlist->getName()))
                );
                $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Something went wrong creating the wish list.')
                );
            }
        }
        parent::addAction();
    }

    /**
     * Display customer wishlist
     */
    public function indexAction()
    {
        /* @var $helper Enterprise_Wishlist_Helper_Data */
        $helper = Mage::helper('Enterprise_Wishlist_Helper_Data');
        if (!$helper->isMultipleEnabled() ) {
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId && $wishlistId != $helper->getDefaultWishlist()->getId() ) {
                $this->_redirectUrl($helper->getListUrl());
            }
        }
        parent::indexAction();
    }

    /**
     * Create new customer wishlist
     */
    public function createwishlistAction()
    {
        $this->_forward('editwishlist');
    }

    /**
     * Edit wishlist
     *
     * @param int $customerId
     * @param string $wishlistName
     * @param bool $visibility
     * @param int $wishlistId
     * @return Mage_Wishlist_Model_Wishlist
     */
    protected function _editWishlist($customerId, $wishlistName, $visibility = false, $wishlistId = null)
    {
        $wishlist = Mage::getModel('Mage_Wishlist_Model_Wishlist');

        if (!$customerId) {
            Mage::throwException(Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Log in to edit wish lists.'));
        }
        if (!strlen($wishlistName)) {
            Mage::throwException(Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Provide wish list name'));
        }
        if ($wishlistId){
            $wishlist->load($wishlistId);
            if ($wishlist->getCustomerId() !== $this->_getSession()->getCustomerId()) {
                Mage::throwException(
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('The wish list is not assigned to your account and cannot be edited.')
                );
            }
        } else {
            $wishlistCollection = Mage::getModel('Mage_Wishlist_Model_Wishlist')->getCollection()
                ->filterByCustomerId($customerId);
            $limit = Mage::helper('Enterprise_Wishlist_Helper_Data')->getWishlistLimit();
            if (Mage::helper('Enterprise_Wishlist_Helper_Data')->isWishlistLimitReached($wishlistCollection)) {
                Mage::throwException(
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Only %d wish lists can be created.', $limit)
                );
            }
            $wishlist->setCustomerId($customerId);
        }
        $wishlist->setName($wishlistName)
            ->setVisibility($visibility)
            ->generateSharingCode()
            ->save();
        return $wishlist;
    }

    /**
     * Edit wishlist properties
     *
     * @return Mage_Core_Controller_Varien_Action|Zend_Controller_Response_Abstract
     */
    public function editwishlistAction()
    {
        $customerId = $this->_getSession()->getCustomerId();
        $wishlistName = $this->getRequest()->getParam('name');
        $visibility = ($this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0);
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        $wishlist = null;
        try {
            $wishlist = $this->_editWishlist($customerId, $wishlistName, $visibility, $wishlistId);

            $this->_getSession()->addSuccess(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Wish List "%s" was saved.', Mage::helper('Mage_Core_Helper_Data')->escapeHtml($wishlist->getName()))
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Something went wrong creating the wish list.')
            );
        }

        if (!$wishlist || !$wishlist->getId()) {
            $this->_getSession()->addError('Could not create wishlist');
        }

        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setHeader('Content-Type', 'application/json');
            $params = array();
            if (!$wishlist->getId()) {
                $params = array('redirect' => Mage::getUrl('*/*'));
            } else {
                $params = array('wishlist_id' => $wishlist->getId());
            }
            return $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($params));
        } else {
            if (!$wishlist || !$wishlist->getId()) {
                return $this->_redirect('*/*');
            } else {
                $this->_redirect('wishlist/index/index', array('wishlist_id' => $wishlist->getId()));
            }
        }
    }

    /**
     * Delete wishlist by id
     *
     * @return void
     */
    public function deletewishlistAction()
    {
        try {
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                return $this->norouteAction();
            }
            if (Mage::helper('Enterprise_Wishlist_Helper_Data')->isWishlistDefault($wishlist)) {
                Mage::throwException(
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('The default wish list cannot be deleted.')
                );
            }
            $wishlist->delete();
            Mage::helper('Mage_Wishlist_Helper_Data')->calculate();
            Mage::getSingleton('Mage_Wishlist_Model_Session')->addSuccess(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Wish list "%s" has been deleted.', Mage::helper('Mage_Core_Helper_Data')->escapeHtml($wishlist->getName()))
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $message = Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Something went wrong deleting the wish list.');
            $this->_getSession()->addException($e, $message);
        }
    }

    /**
     * Build wishlist product name list string
     *
     * @param array $items
     * @return string
     */
    protected function _joinProductNames($items)
    {
        $names = array();
        foreach ($items as $item) {
            $names[] = '"' . $item->getProduct()->getName() . '"';
        }
        return join(', ', $names);
    }

    /**
     * Copy item to given wishlist
     *
     * @param Mage_Wishlist_Model_Item $item
     * @param Mage_Wishlist_Model_Wishlist $wishlist
     * @param int $qty
     * @throws InvalidArgumentException|DomainException
     */
    protected function _copyItem(Mage_Wishlist_Model_Item $item, Mage_Wishlist_Model_Wishlist $wishlist, $qty = null)
    {
        if (!$item->getId()) {
            throw new InvalidArgumentException();
        }
        if ($item->getWishlistId() == $wishlist->getId()) {
            throw new DomainException();
        }
        $buyRequest = $item->getBuyRequest();
        if ($qty) {
            $buyRequest->setQty($qty);
        }
        $wishlist->addNewItem($item->getProduct(), $buyRequest);
        $this->_eventManager->dispatch(
            'wishlist_add_product',
            array(
                'wishlist'  => $wishlist,
                'product'   => $item->getProduct(),
                'item'      => $item
            )
        );
    }

    /**
     * Copy wishlist item to given wishlist
     *
     * @return void
     */
    public function copyitemAction()
    {
        $session = $this->_getSession();
        $requestParams = $this->getRequest()->getParams();
        if ($session->getBeforeWishlistRequest()) {
            $requestParams = $session->getBeforeWishlistRequest();
            $session->unsBeforeWishlistRequest();
        }

        $wishlist = $this->_getWishlist(isset($requestParams['wishlist_id']) ? $requestParams['wishlist_id'] : null);
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemId = isset($requestParams['item_id']) ? $requestParams['item_id'] : null;
        $qty = isset($requestParams['qty']) ? $requestParams['qty'] : null;
        if ($itemId) {
            $productName = '';
            try {
                /* @var Mage_Wishlist_Model_Item $item */
                $item = Mage::getModel('Mage_Wishlist_Model_Item');
                $item->loadWithOptions($itemId);

                $wishlistName = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($wishlist->getName());
                $productName = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($item->getProduct()->getName());

                $this->_copyItem($item, $wishlist, $qty);
                $this->_getSession()->addSuccess(
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('"%s" was copied to %s.', $productName, $wishlistName)
                );
                Mage::helper('Mage_Wishlist_Helper_Data')->calculate();
            } catch (InvalidArgumentException $e) {
                $this->_getSession->addError(
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('The item was not found.')
                );
            } catch (DomainException $e) {
                $this->_getSession()->addError(
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('"%s" is already present in %s.', $productName, $wishlistName)
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                if ($productName) {
                    $message = Mage::helper('Enterprise_Wishlist_Helper_Data')->__('We could not copy "%s".', $productName);
                } else {
                    $message = Mage::helper('Enterprise_Wishlist_Helper_Data')->__('We could not copy the wish list item.');
                }
                $this->_getSession()->addError($message);
            }
        }
        $wishlist->save();
        if ($this->_getSession()->hasBeforeWishlistUrl())
        {
            $this->_redirectUrl($this->_getSession()->getBeforeWishlistUrl());
            $this->_getSession()->unsBeforeWishlistUrl();
        } else {
            $this->_redirectReferer();
        }
    }

    /**
     * Copy wishlist items to given wishlist
     *
     * @return void
     */
    public function copyitemsAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemIds = $this->getRequest()->getParam('selected', array());
        $notFound = array();
        $alreadyPresent = array();
        $failed = array();
        $copied = array();
        if (count($itemIds)) {
            $qtys = $this->getRequest()->getParam('qty', array());
            foreach ($itemIds as $id => $value) {
                try {
                    /* @var Mage_Wishlist_Model_Item $item */
                    $item = Mage::getModel('Mage_Wishlist_Model_Item');
                    $item->loadWithOptions($id);

                    $this->_copyItem($item, $wishlist, isset($qtys[$id]) ? $qtys[$id] : null);
                    $copied[$id] = $item;
                } catch (InvalidArgumentException $e) {
                    $notFound[] = $id;
                } catch (DomainException $e) {
                    $alreadyPresent[$id] = $item;
                } catch (Exception $e) {
                    Mage::logException($e);
                    $failed[] = $id;
                }
            }
        }
        $wishlistName = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($wishlist->getName());

        $wishlist->save();

        if (count($notFound)) {
            $this->_getSession()->addError(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('%d items were not found.', count($notFound))
            );
        }

        if (count($failed)) {
            $this->_getSession()->addError(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('We could not copy %d items.', count($failed))
            );
        }

        if (count($alreadyPresent)) {
            $names = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($this->_joinProductNames($alreadyPresent));
            $this->_getSession()->addError(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('%d items are already present in %s: %s.', count($alreadyPresent), $wishlistName, $names)
            );
        }

        if (count($copied)) {
            Mage::helper('Mage_Wishlist_Helper_Data')->calculate();
            $names = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($this->_joinProductNames($copied));
            $this->_getSession()->addSuccess(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('%d items were copied to %s: %s.', count($copied), $wishlistName, $names)
            );
        }
        $this->_redirectReferer();
    }

    /**
     * Move item to given wishlist.
     * Check whether item belongs to one of customer's wishlists
     *
     * @param Mage_Wishlist_Model_Item $item
     * @param Mage_Wishlist_Model_Wishlist $wishlist
     * @param Mage_Wishlist_Model_Resource_Wishlist_Collection $customerWishlists
     * @param int $qty
     * @throws InvalidArgumentException|DomainException
     */
    protected function _moveItem(
        Mage_Wishlist_Model_Item $item,
        Mage_Wishlist_Model_Wishlist $wishlist,
        Mage_Wishlist_Model_Resource_Wishlist_Collection $customerWishlists,
        $qty = null
    ) {
        if (!$item->getId()) {
            throw new InvalidArgumentException();
        }
        if ($item->getWishlistId() == $wishlist->getId()) {
            throw new DomainException(null, 1);
        }
        if (!$customerWishlists->getItemById($item->getWishlistId())) {
            throw new DomainException(null, 2);
        }

        $buyRequest = $item->getBuyRequest();
        if ($qty) {
            $buyRequest->setQty($qty);
        }
        $wishlist->addNewItem($item->getProduct(), $buyRequest);
        $qtyDiff = $item->getQty() - $qty;
        if ($qty && $qtyDiff > 0) {
            $item->setQty($qtyDiff);
            $item->save();
        } else {
            $item->delete();
        }
    }

    /**
     * Move wishlist item to given wishlist
     *
     * @return void
     */
    public function moveitemAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemId = $this->getRequest()->getParam('item_id');

        if ($itemId) {
            try {
                $wishlists = Mage::getModel('Mage_Wishlist_Model_Wishlist')->getCollection()
                    ->filterByCustomerId($this->_getSession()->getCustomerId());

                /* @var Mage_Wishlist_Model_Item $item */
                $item = Mage::getModel('Mage_Wishlist_Model_Item');
                $item->loadWithOptions($itemId);

                $productName = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($item->getProduct()->getName());
                $wishlistName = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($wishlist->getName());

                $this->_moveItem($item, $wishlist, $wishlists, $this->getRequest()->getParam('qty', null));
                $this->_getSession()->addSuccess(
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('"%s" was moved to %s.', $productName, $wishlistName)
                );
                Mage::helper('Mage_Wishlist_Helper_Data')->calculate();
            } catch (InvalidArgumentException $e) {
                $this->_getSession()->addError(
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__("An item with this ID doesn't exist.")
                );
            } catch (DomainException $e) {
                if ($e->getCode() == 1) {
                    $this->_getSession()->addError(
                        Mage::helper('Enterprise_Wishlist_Helper_Data')->__('"%s" is already present in %s.', $productName, $wishlistName)
                    );
                } else {
                    $this->_getSession()->addError(
                        Mage::helper('Enterprise_Wishlist_Helper_Data')->__('We cannot move "%s".', $productName)
                    );
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('We could not move the wish list item.')
                );
            }
        }
        $wishlist->save();
        $this->_redirectReferer();
    }

    /**
     * Move wishlist items to given wishlist
     */
    public function moveitemsAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemIds = $this->getRequest()->getParam('selected', array());
        $moved = array();
        $failed = array();
        $notFound = array();
        $notAllowed = array();
        $alreadyPresent = array();
        if (count($itemIds)) {
            $wishlists = Mage::getModel('Mage_Wishlist_Model_Wishlist')->getCollection();
            $wishlists->filterByCustomerId($this->_getSession()->getCustomerId());
            $qtys = $this->getRequest()->getParam('qty', array());

            foreach ($itemIds as $id => $value) {
                try {
                    /* @var Mage_Wishlist_Model_Item $item */
                    $item = Mage::getModel('Mage_Wishlist_Model_Item');
                    $item->loadWithOptions($id);

                    $this->_moveItem($item, $wishlist, $wishlists, isset($qtys[$id]) ? $qtys[$id] : null);
                    $moved[$id] = $item;
                } catch (InvalidArgumentException $e) {
                    $notFound[] = $id;
                } catch (DomainException $e) {
                    if ($e->getCode() == 1) {
                        $alreadyPresent[$id] = $item;
                    } else {
                        $notAllowed[$id] = $item;
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    $failed[] = $id;
                }
            }
        }

        $wishlistName = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($wishlist->getName());

        if (count($notFound)) {
            $this->_getSession()->addError(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('%d items were not found.', count($notFound))
            );
        }

        if (count($notAllowed)) {
            $names = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($this->_joinProductNames($notAllowed));
            $this->_getSession()->addError(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('%d items cannot be moved: %s.', count($notAllowed), $names)
            );
        }

        if (count($alreadyPresent)) {
            $names = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($this->_joinProductNames($alreadyPresent));
            $this->_getSession()->addError(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('%d items are already present in %s: %s.', count($alreadyPresent), $wishlistName, $names)
            );
        }

        if (count($failed)) {
            $this->_getSession()->addError(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('We could not move %d items.', count($failed))
            );
        }

        if (count($moved)) {
            Mage::helper('Mage_Wishlist_Helper_Data')->calculate();
            $names = Mage::helper('Mage_Core_Helper_Data')->escapeHtml($this->_joinProductNames($moved));
            $this->_getSession()->addSuccess(
                Mage::helper('Enterprise_Wishlist_Helper_Data')->__('%d items were moved to %s: %s.', count($moved), $wishlistName, $names)
            );
        }
        $this->_redirectReferer();
    }
}
