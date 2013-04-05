<?php
/**
 * Adminhtml AdminNotification controller
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Adminhtml_NotificationController extends Mage_Backend_Controller_ActionAbstract
{
    public function indexAction()
    {
        $this->_title($this->__('Notifications'));

        $this->loadLayout()
            ->_setActiveMenu('Mage_AdminNotification::system_adminnotification')
            ->_addBreadcrumb(
                Mage::helper('Mage_AdminNotification_Helper_Data')->__('Messages Inbox'),
                Mage::helper('Mage_AdminNotification_Helper_Data')->__('Messages Inbox')
            )->renderLayout();
    }

    public function markAsReadAction()
    {
        $notificationId = (int)$this->getRequest()->getParam('id');
        if ($notificationId) {
            $session = Mage::getSingleton('Mage_Backend_Model_Session');
            try {
                $this->_objectManager->create('Mage_AdminNotification_Model_NotificationService')
                    ->markAsRead($notificationId);
                $session->addSuccess(
                    Mage::helper('Mage_AdminNotification_Helper_Data')->__('The message has been marked as read.')
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e,
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__('An error occurred while marking notification as read.')
                );
            }

            $this->_redirectReferer();
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Mark notification as read (AJAX action)
     */
    public function ajaxMarkAsReadAction()
    {
        if (!$this->getRequest()->getPost()) {
            return;
        }
        $notificationId = (int)$this->getRequest()->getPost('id');
        $responseData = array();
        try {
            $this->_objectManager->create('Mage_AdminNotification_Model_NotificationService')
                ->markAsRead($notificationId);
            $responseData['success'] = true;
        } catch (Exception $e) {
            $responseData['success'] = false;
        }
        $this->getResponse()->setBody(
            $this->_objectManager->create('Mage_Core_Helper_Data')->jsonEncode($responseData)
        );
    }

    public function massMarkAsReadAction()
    {
        $session = Mage::getSingleton('Mage_Backend_Model_Session');
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $session->addError(Mage::helper('Mage_AdminNotification_Helper_Data')->__('Please select messages.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('Mage_AdminNotification_Model_Inbox')
                        ->load($id);
                    if ($model->getId()) {
                        $model->setIsRead(1)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__('Total of %d record(s) have been marked as read.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e,
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__('An error occurred while marking the messages as read.')
                );
            }
        }
        $this->_redirect('*/*/');
    }

    public function removeAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $session = Mage::getSingleton('Mage_Backend_Model_Session');
            $model = Mage::getModel('Mage_AdminNotification_Model_Inbox')
                ->load($id);

            if (!$model->getId()) {
                $this->_redirect('*/*/');
                return ;
            }

            try {
                $model->setIsRemove(1)
                    ->save();
                $session->addSuccess(
                    Mage::helper('Mage_AdminNotification_Helper_Data')->__('The message has been removed.')
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e,
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__('An error occurred while removing the message.')
                );
            }

            $this->_redirect('*/*/');
            return;
        }
        $this->_redirect('*/*/');
    }

    public function massRemoveAction()
    {
        $session = Mage::getSingleton('Mage_Backend_Model_Session');
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $session->addError(Mage::helper('Mage_AdminNotification_Helper_Data')->__('Please select messages.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('Mage_AdminNotification_Model_Inbox')
                        ->load($id);
                    if ($model->getId()) {
                        $model->setIsRemove(1)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__('Total of %d record(s) have been removed.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e,
                    Mage::helper('Mage_AdminNotification_Helper_Data')
                        ->__('An error occurred while removing messages.'));
            }
        }
        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'markAsRead':
                $acl = 'Mage_AdminNotification::mark_as_read';
                break;

            case 'massMarkAsRead':
                $acl = 'Mage_AdminNotification::mark_as_read';
                break;

            case 'remove':
                $acl = 'Mage_AdminNotification::adminnotification_remove';
                break;

            case 'massRemove':
                $acl = 'Mage_AdminNotification::adminnotification_remove';
                break;

            default:
                $acl = 'Mage_AdminNotification::show_list';
        }
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed($acl);
    }
}
