<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftRegistry_Adminhtml_Giftregistry_CustomerController extends Enterprise_Enterprise_Controller_Adminhtml_Action
{
    protected function _initEntity($requestParam = 'id')
    {
        $entity = Mage::getModel('enterprise_giftregistry/entity');
        if ($entityId = $this->getRequest()->getParam($requestParam)) {
            $entity->load($entityId);
            if (!$entity->getId()) {
                Mage::throwException($this->__('Wrong gift registry entity requested.'));
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
        $block = $this->getLayout()->createBlock('enterprise_giftregistry/adminhtml_customer_grid');
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Get customer gift registry info block
     */
    public function editAction()
    {
        $this->_initEntity();
        $this->_title($this->__('Gift Registry Entity'));
        $this->loadLayout()->renderLayout();
    }

    /**
     * Add quote items to gift registry
     */
    public function addAction()
    {
        if ($quoteIds = $this->getRequest()->getParam('products')){
            $model = $this->_initEntity();
            try {
                $model->addQuoteItems($quoteIds);
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Shopping cart items have been added to gift registry.'));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('Failed to add shopping cart items to gift registry.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/edit', array('id' => $model->getId()));
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

            if (Mage::app()->isSingleStoreMode()) {
                $storeId = Mage::app()->getStore(true)->getId();
            } else {
                $storeId = $this->getRequest()->getParam('store_id');
            }

            try {
                $sentCount   = 0;
                $failedCount = 0;
                foreach ($emails as $email) {
                    if (!empty($email)) {
                        if ($model->sendShareEmail($email, $storeId, $this->getRequest()->getParam('message'))) {
                            $sentCount++;
                        } else {
                            $failedCount++;
                        }
                        $emailsForSend[] = $email;
                    }
                }
                if (empty($emailsForSend)) {
                    Mage::throwException($this->__('Please specify at least one email.'));
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }

            if ($sentCount) {
                $this->_getSession()->addSuccess($this->__('%d email(s) were sent.', $sentCount));
            }
            if ($failedCount) {
                $this->_getSession()->addError($this->__('Failed to send %1$d of %2$d email(s).', $failedCount, count($emailsForSend)));
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
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The gift registry entity has been deleted.'));
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $model->getId()));
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Failed to delete gift registry entity.'));
            Mage::logException($e);
        }
        $this->_redirect('*/customer/edit', array('id' => $customerId, 'active_tab' => 'giftregistry'));
    }
}
