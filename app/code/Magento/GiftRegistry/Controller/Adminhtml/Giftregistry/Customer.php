<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Registry controller
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry;

class Customer extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
    }

    protected function _initEntity($requestParam = 'id')
    {
        $entity = $this->_objectManager->create('Magento\GiftRegistry\Model\Entity');
        $entityId = $this->getRequest()->getParam($requestParam);
        if ($entityId) {
            $entity->load($entityId);
            if (!$entity->getId()) {
                throw new \Magento\Core\Exception(__('Please correct the gift registry entity.'));
            }
        }
        $this->_coreRegistry->register('current_giftregistry_entity', $entity);
        return $entity;
    }

    /**
     * Get customer gift registry grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get customer gift registry info block
     */
    public function editAction()
    {
        try {
            $model = $this->_initEntity();
            $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($model->getCustomerId());

            $this->_title(__('Customers'))
                ->_title(__('Customers'))
                ->_title($customer->getName())
                ->_title(__("Edit '%1' Gift Registry", $model->getTitle()));

            $this->loadLayout()->renderLayout();
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            $this->_redirect('adminhtml/customer/edit', array(
                'id'         => $this->getRequest()->getParam('customer'),
                'active_tab' => 'giftregistry'
            ));
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')
                ->addError(__('Something went wrong while editing the gift registry.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->_redirect('adminhtml/customer/edit', array(
                'id'         => $this->getRequest()->getParam('customer'),
                'active_tab' => 'giftregistry'
            ));
        }
    }

    /**
     * Add quote items to gift registry
     */
    public function addAction()
    {
        if ($quoteIds = $this->getRequest()->getParam('products')){
            $model = $this->_initEntity();
            try {
                $skippedItems = $model->addQuoteItems($quoteIds);
                if (count($quoteIds) - $skippedItems > 0) {
                    $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(
                        __('Shopping cart items have been added to gift registry.')
                    );
                }
                if ($skippedItems) {
                    $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addNotice(
                        __('Virtual, Downloadable, and virtual Gift Card products cannot be added to gift registries.')
                    );
                }
            } catch (\Magento\Core\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                return;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')
                    ->addError(__('Failed to add shopping cart items to gift registry.'));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
    }

    /**
     * Update gift registry items qty
     */
    public function updateAction()
    {
        $items = $this->getRequest()->getParam('items');
        $entity = $this->_initEntity();
        $updatedCount = 0;

        if (is_array($items)) {
            try {
                $model = $this->_objectManager->create('Magento\GiftRegistry\Model\Item');
                foreach ($items as $itemId => $data) {
                    if (!empty($data['action'])) {
                        $model->load($itemId);
                        if ($model->getId() && $model->getEntityId() == $entity->getId()) {
                            if ($data['action'] == 'remove') {
                                $model->delete();
                            } else {
                                $model->setQty($data['qty']);
                                $model->save();
                            }
                        }
                        $updatedCount++;
                    }
                }
                if ($updatedCount) {
                    $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(
                        __('You updated this gift registry.')
                    );
                }
            } catch (\Magento\Core\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('id' => $entity->getId()));
                return;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__("We couldn't update these gift registry items."));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/edit', array('id' => $entity->getId()));
    }

    /**
     * Share gift registry action
     */
    public function shareAction()
    {
        $model = $this->_initEntity();
        $data = $this->getRequest()->getParam('emails');
        if ($data) {
            $emails = explode(',', $data);
            $emailsForSend = array();

            if ($this->storeManager->hasSingleStore()) {
                $storeId = $this->storeManager->getStore(true)->getId();
            } else {
                $storeId = $this->getRequest()->getParam('store_id');
            }
            $model->setStoreId($storeId);

            try {
                $sentCount   = 0;
                $failedCount = 0;
                foreach ($emails as $email) {
                    if (!empty($email)) {
                        if ($model->sendShareRegistryEmail(
                                $email,
                                $storeId,
                                $this->getRequest()->getParam('message')
                            )
                        ) {
                            $sentCount++;
                        } else {
                            $failedCount++;
                        }
                        $emailsForSend[] = $email;
                    }
                }
                if (empty($emailsForSend)) {
                    throw new \Magento\Core\Exception(__('Please enter at least one email address.'));
                }
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }

            if ($sentCount) {
                $this->_getSession()->addSuccess(__('%1 email(s) were sent.', $sentCount));
            }
            if ($failedCount) {
                $this->_getSession()->addError(
                    __("We couldn't send '%1 of %2 emails.", $failedCount, count($emailsForSend))
                );
            }
        }
        $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
    }

    /**
     * Delete gift registry action
     */
    public function deleteAction()
    {
        try {
            $model = $this->_initEntity();
            $customerId = $model->getCustomerId();
            $model->delete();
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(
                __('You deleted this gift registry entity.')
            );
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
            return;
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__("We couldn't delete this gift registry entity."));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('adminhtml/customer/edit', array('id' => $customerId, 'active_tab' => 'giftregistry'));
    }

    /**
     * Check the permission
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GiftRegistry::customer_magento_giftregistry');
    }
}
