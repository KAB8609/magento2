<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Adminhtml_GiftcardaccountController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Defines if status message of code pool is show
     *
     * @var bool
     */
    protected $_showCodePoolStatusMessage = true;

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Gift Card Accounts'));

        if ($this->_showCodePoolStatusMessage) {
            $usage = Mage::getModel('Enterprise_GiftCardAccount_Model_Pool')->getPoolUsageInfo();

            $function = 'addNotice';
            if ($usage->getPercent() == 100) {
                $function = 'addError';
            }

            $url = Mage::getSingleton('Mage_Adminhtml_Model_Url')->getUrl('*/*/generate');
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->$function(
                Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Code Pool used: <b>%.2f%%</b> (free <b>%d</b> of <b>%d</b> total). Generate new code pool <a href="%s">here</a>.', $usage->getPercent(), $usage->getFree(), $usage->getTotal(), $url)
            );
        }

        $this->loadLayout();
        $this->_setActiveMenu('customer/giftcardaccount');
        $this->renderLayout();
    }


    /**
     * Create new Gift Card Account
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit GiftCardAccount
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_initGca();

        if (!$model->getId() && $id) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('This Gift Card Account no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($model->getId() ? $model->getCode() : $this->__('New Account'));

        $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->loadLayout()
            ->_addBreadcrumb($id ? Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Edit Gift Card Account') : Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('New Gift Card Account'),
                             $id ? Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Edit Gift Card Account') : Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('New Gift Card Account'))
            ->_addContent(
                $this->getLayout()->createBlock('Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit')
                    ->setData('form_action_url', $this->getUrl('*/*/save'))
            )
            ->_addLeft(
                $this->getLayout()->createBlock('Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tabs')
            )
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            // init model and set data
            $id = $this->getRequest()->getParam('giftcardaccount_id');
            $model = $this->_initGca('giftcardaccount_id');
            if (!$model->getId() && $id) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('This Gift Card Account no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            if (!empty($data)) {
                $model->addData($data);
            }

            // try to save it
            try {
                // save the data
                $model->save();
                $sending = null;
                $status = null;

                if ($model->getSendAction()) {
                    try {
                        if($model->getStatus()){
                            $model->sendEmail();
                            $sending = $model->getEmailSent();
                        }
                        else {
                            $status = true;
                        }
                    } catch (Exception $e) {
                        $sending = false;
                    }
                }

                if (!is_null($sending)) {
                    if ($sending) {
                        Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('The gift card account has been saved and sent.'));
                    } else {
                        Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('The gift card account has been saved, but email was not sent.'));
                    }
                } else {
                    Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('The gift card account has been saved.'));

                    if ($status) {
                        Mage::getSingleton('Mage_Adminhtml_Model_Session')->addNotice(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Email was not sent because the gift card account is not active.'));
                    }
                }

                // clear previously saved data from session
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Gift Card Account has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Unable to find a Gift Card Account to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Render GCA grid
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Grid',
                'giftcardaccount.grid'
            )
            ->toHtml()
        );
    }

    /**
     * Generate code pool
     */
    public function generateAction()
    {
        try {
            Mage::getModel('Enterprise_GiftCardAccount_Model_Pool')->generatePool();
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('New code pool was generated.'));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addException($e, Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Unable to generate new code pool.'));
        }
        $this->_redirectReferer('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('customer/giftcardaccount');
    }

    /**
     * Render GCA history grid
     */
    public function gridHistoryAction()
    {
        $model = $this->_initGca();
        $id = (int)$this->getRequest()->getParam('id');
        if ($id && !$model->getId()) {
            return;
        }

        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_History')
                ->toHtml()
        );
    }

    /**
     * Load GCA from request
     *
     * @param string $idFieldName
     */
    protected function _initGca($idFieldName = 'id')
    {
        $this->_title($this->__('Customers'))->_title($this->__('Gift Card Accounts'));

        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount');
        if ($id) {
            $model->load($id);
        }
        Mage::register('current_giftcardaccount', $model);
        return $model;
    }

    /**
     * Export GCA grid to MSXML
     */
    public function exportMsxmlAction()
    {
        $this->_prepareDownloadResponse('giftcardaccounts.xml',
            $this->getLayout()->createBlock('Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Grid')
                ->getExcelFile($this->__('Gift Card Accounts'))
        );
    }

    /**
     * Export GCA grid to CSV
     */
    public function exportCsvAction()
    {
        $this->_prepareDownloadResponse('giftcardaccounts.csv',
            $this->getLayout()->createBlock(
                'Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Grid'
            )->getCsvFile()
        );
    }

    /**
     * Delete gift card accounts specified using grid massaction
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('giftcardaccount');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select gift card account(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('Enterprise_GiftCardAccount_Model_Giftcardaccount')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($ids))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('date_expires'));

        return $data;
    }

    /**
     * Setter for code pool status message flag
     *
     * @param bool $isShow
     */
    public function setShowCodePoolStatusMessage($isShow)
    {
        $this->_showCodePoolStatusMessage = (bool)$isShow;
    }
}
