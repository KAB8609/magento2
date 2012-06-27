<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Wrapping Controller
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Adminhtml_GiftwrappingController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init active menu
     *
     * @return Enterprise_GiftWrapping_Adminhtml_GiftwrappingController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Enterprise_GiftWrapping::sales_enterprise_giftwrapping');

        $this->_title(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Sales'))->_title(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Manage Gift Wrapping'));
        return $this;
    }

    /**
     * Init model
     *
     * @return Enterprise_Giftwrapping_Model_Wrapping
     */
    protected function _initModel($requestParam = 'id')
    {
        $model = Mage::registry('current_giftwrapping_model');
        if ($model) {
           return $model;
        }
        $model = Mage::getModel('Enterprise_GiftWrapping_Model_Wrapping');
        $model->setStoreId($this->getRequest()->getParam('store', 0));

        $wrappingId = $this->getRequest()->getParam($requestParam);
        if ($wrappingId) {
            $model->load($wrappingId);
            if (!$model->getId()) {
                Mage::throwException(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Wrong gift wrapping requested.'));
            }
        }
        Mage::register('current_giftwrapping_model', $model);

        return $model;
    }

    /**
     * List of gift wrappings
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    /**
     * Create new gift wrapping
     *
     * @return void
     */
    public function newAction()
    {
        $model = $this->_initModel();
        $this->_initAction();
        $this->_title(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('New Gift Wrapping'));
        $this->renderLayout();
    }

    /**
     * Edit gift wrapping
     *
     * @return void
     */
    public function editAction()
    {
        $model = $this->_initModel();
        $this->_initAction();
        if ($formData = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getFormData()) {
            $model->addData($formData);
        }
        $this->_title(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Edit Gift Wrapping "%s"', $model->getDesign()));
        $this->renderLayout();
    }

    /**
     * Save gift wrapping
     *
     * @return void
     */
    public function saveAction()
    {
        $wrappingRawData = $this->_prepareGiftWrappingRawData($this->getRequest()->getPost('wrapping'));
        if ($wrappingRawData) {
            try {
                $model = $this->_initModel();
                $model->addData($wrappingRawData);

                $data = new Varien_Object($wrappingRawData);
                if ($data->getData('image_name/delete')) {
                    $model->setImage('');
                    // Delete temporary image if exists
                    $model->unsTmpImage();
                } else {
                    try {
                        $model->attachUploadedImage('image_name');
                    } catch (Exception $e) {
                        Mage::throwException(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Image has not been uploaded.'));
                    }
                }

                $model->save();
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('The gift wrapping has been saved.'));

                $redirectBack = $this->getRequest()->getParam('back', false);
                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'store' => $model->getStoreId()));
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Failed to save gift wrapping.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Upload temporary gift wrapping image
     *
     * @return void
     */
    public function uploadAction()
    {
        $wrappingRawData = $this->_prepareGiftWrappingRawData($this->getRequest()->getPost('wrapping'));
        if ($wrappingRawData) {
            try {
                $model = $this->_initModel();
                $model->addData($wrappingRawData);
                try {
                    $model->attachUploadedImage('image_name', true);
                } catch (Exception $e) {
                    Mage::throwException(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Image was not uploaded.'));
                }
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_getSession()->setFormData($wrappingRawData);
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Failed to save gift wrapping.'));
                Mage::logException($e);
            }
        }

        if (isset($model) && $model->getId()) {
            $this->_forward('edit');
        } else {
            $this->_forward('new');
        }
    }

    /**
     * Change gift wrapping(s) status action
     *
     * @return void
     */
    public function changeStatusAction()
    {
        $wrappingIds = (array)$this->getRequest()->getParam('wrapping_ids');
        $status = (int)(bool)$this->getRequest()->getParam('status');
        try {
            $wrappingCollection = Mage::getModel('Enterprise_GiftWrapping_Model_Wrapping')->getCollection();
            $wrappingCollection->addFieldToFilter('wrapping_id', array('in' => $wrappingIds));
            foreach ($wrappingCollection as $wrapping) {
                $wrapping->setStatus($status);
            }
            $wrappingCollection->save();
            $this->_getSession()->addSuccess(
                Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Total of %d record(s) have been updated.', count($wrappingIds))
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('An error occurred while updating the wrapping(s) status.'));
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Delete specified gift wrapping(s)
     * This action can be performed on 'Manage Gift Wrappings' page
     *
     * @return void
     */
    public function massDeleteAction()
    {
        $wrappingIds = (array)$this->getRequest()->getParam('wrapping_ids');
        if (!is_array($wrappingIds)) {
            $this->_getSession()->addError(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Please select items.'));
        } else {
            try {
                $wrappingCollection = Mage::getModel('Enterprise_GiftWrapping_Model_Wrapping')->getCollection();
                $wrappingCollection->addFieldToFilter('wrapping_id', array('in' => $wrappingIds));
                foreach ($wrappingCollection as $wrapping) {
                    $wrapping->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Total of %d record(s) have been deleted.', count($wrappingIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Delete current gift wrapping
     * This action can be performed on 'Edit Gift Wrapping' page
     *
     * @return void
     */
    public function deleteAction()
    {
        $wrapping = Mage::getModel('Enterprise_GiftWrapping_Model_Wrapping');
        $wrapping->load($this->getRequest()->getParam('id', false));
        if ($wrapping->getId()) {
            try {
                $wrapping->delete();
                $this->_getSession()->addSuccess(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('The gift wrapping has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current'=>true));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/enterprise_giftwrapping');
    }

    /**
     * Prepare Gift Wrapping Raw data
     *
     * @param array $wrappingRawData
     * @return array
     */
    protected function _prepareGiftWrappingRawData($wrappingRawData)
    {
        if (isset($wrappingRawData['tmp_image'])) {
            $wrappingRawData['tmp_image'] = basename($wrappingRawData['tmp_image']);
        }
        if (isset($wrappingRawData['image_name']['value'])) {
            $wrappingRawData['image_name']['value'] = basename($wrappingRawData['image_name']['value']);
        }
        return $wrappingRawData;
    }

    /**
     * Ajax action for GiftWrapping content in backend order creation
     *
     * @deprecated since 1.12.0.0
     *
     * @return void
     */
    public function orderOptionsAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
}
