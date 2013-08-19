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
class Magento_GiftRegistry_Controller_Adminhtml_Giftregistry_Customer extends Magento_Adminhtml_Controller_Action
{
    protected function _initEntity($requestParam = 'id')
    {
        $entity = Mage::getModel('Magento_GiftRegistry_Model_Entity');
        if ($entityId = $this->getRequest()->getParam($requestParam)) {
            $entity->load($entityId);
            if (!$entity->getId()) {
                Mage::throwException($this->__('Please correct the gift registry entity.'));
            }
        }
        Mage::register('current_giftregistry_entity', $entity);
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
            $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($model->getCustomerId());

            $this->_title($this->__('Customers'))
                ->_title($this->__('Customers'))
                ->_title($customer->getName())
                ->_title($this->__("Edit '%s' Gift Registry", $model->getTitle()));

            $this->loadLayout()->renderLayout();
        } catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            $this->_redirect('*/customer/edit', array(
                'id'         => $this->getRequest()->getParam('customer'),
                'active_tab' => 'giftregistry'
            ));
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')
                ->addError($this->__('Something went wrong while editing the gift registry.'));
            Mage::logException($e);
            $this->_redirect('*/customer/edit', array(
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
                    Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                        $this->__('Shopping cart items have been added to gift registry.')
                    );
                }
                if ($skippedItems) {
                    Mage::getSingleton('Magento_Adminhtml_Model_Session')->addNotice(
                        $this->__('Virtual, Downloadable, and virtual Gift Card products cannot be added to gift registries.')
                    );
                }
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')
                    ->addError($this->__('Failed to add shopping cart items to gift registry.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/edit', array('id' => $model->getId()));
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
                $model = Mage::getModel('Magento_GiftRegistry_Model_Item');
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
                    Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                        $this->__('You updated this gift registry.')
                    );
                }
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $entity->getId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($this->__("We couldn't update these gift registry items."));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/edit', array('id' => $entity->getId()));
    }

    /**
     * Share gift registry action
     */
    public function shareAction()
    {
        $model = $this->_initEntity();

        if ($data = $this->getRequest()->getParam('emails')) {
            $emails = explode(',', $data);
            $emailsForSend = array();

            if (Mage::app()->hasSingleStore()) {
                $storeId = Mage::app()->getStore(true)->getId();
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
                    Mage::throwException($this->__('Please enter at least one email address.'));
                }
            }
            catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }

            if ($sentCount) {
                $this->_getSession()->addSuccess($this->__('%d email(s) were sent.', $sentCount));
            }
            if ($failedCount) {
                $this->_getSession()->addError(
                    $this->__("We couldn't send '%d of %d emails.", $failedCount, count($emailsForSend))
                );
            }
        }
        $this->_redirect('*/*/edit', array('id' => $model->getId()));
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
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                $this->__('You deleted this gift registry entity.')
            );
        }
        catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $model->getId()));
            return;
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($this->__("We couldn't delete this gift registry entity."));
            Mage::logException($e);
        }
        $this->_redirect('*/customer/edit', array('id' => $customerId, 'active_tab' => 'giftregistry'));
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
