<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleShopping\Model;

/**
 * Google Shopping Observer
 */
class Observer
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Admin session
     *
     * @var \Magento\Session\SessionManagerInterface
     */
    protected $_session;

    /**
     * Admin session
     *
     * @var \Magento\GoogleShopping\Model\Flag
     */
    protected $_flag;

    /**
     * Mass operations factory
     *
     * @var \Magento\GoogleShopping\Model\MassOperationsFactory
     */
    protected $_operationsFactory;

    /**
     * Inbox factory
     *
     * @var \Magento\AdminNotification\Model\InboxFactory
     */
    protected $_inboxFactory;

    /**
     * Collection factory
     *
     * @var \Magento\GoogleShopping\Model\Resource\Item\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\GoogleShopping\Model\Resource\Item\CollectionFactory $collectionFactory
     * @param \Magento\GoogleShopping\Model\MassOperationsFactory $operationsFactory
     * @param \Magento\AdminNotification\Model\InboxFactory $inboxFactory
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Session\SessionManagerInterface $session
     * @param \Magento\GoogleShopping\Model\Flag $flag
     */
    public function __construct(
        \Magento\GoogleShopping\Model\Resource\Item\CollectionFactory $collectionFactory,
        \Magento\GoogleShopping\Model\MassOperationsFactory $operationsFactory,
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Session\SessionManagerInterface $session,
        \Magento\GoogleShopping\Model\Flag $flag
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_operationsFactory = $operationsFactory;
        $this->_inboxFactory = $inboxFactory;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_session = $session;
        $this->_flag = $flag;
    }

    /**
     * Update product item in Google Content
     *
     * @param \Magento\Object $observer
     * @return \Magento\GoogleShopping\Model\Observer
     */
    public function saveProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items = $this->_getItemsCollection($product);

        try {
            $this->_operationsFactory->create()->synchronizeItems($items);
        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_session->addError('Cannot update Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Delete product item from Google Content
     *
     * @param \Magento\Object $observer
     * @return \Magento\GoogleShopping\Model\Observer
     */
    public function deleteProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items = $this->_getItemsCollection($product);

        try {
            $this->_operationsFactory->create()->deleteItems($items);
        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_session->addError('Cannot delete Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Get items which are available for update/delete when product is saved
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\GoogleShopping\Model\Resource\Item\Collection
     */
    protected function _getItemsCollection($product)
    {
        $items = $this->_collectionFactory->create()->addProductFilterId($product->getId());
        if ($product->getStoreId()) {
            $items->addStoreFilter($product->getStoreId());
        }

        foreach ($items as $item) {
            if (!$this->_coreStoreConfig->getConfigFlag('google/googleshopping/observed', $item->getStoreId())) {
                $items->removeItemByKey($item->getId());
            }
        }

        return $items;
    }

    /**
     * Check if synchronize process is finished and generate notification message
     *
     * @param  \Magento\Event\Observer $observer
     * @return \Magento\GoogleShopping\Model\Observer
     */
    public function checkSynchronizationOperations(\Magento\Event\Observer $observer)
    {
        $flag = $this->_flag->loadSelf();
        if ($flag->isExpired()) {
            $this->_inboxFactory->create()->addMajor(
                __('Google Shopping operation has expired.'),
                __('One or more google shopping synchronization operations failed because of timeout.')
            );
            $flag->unlock();
        }
        return $this;
    }
}
