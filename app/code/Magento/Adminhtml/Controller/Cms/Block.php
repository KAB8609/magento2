<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms manage blocks controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Cms_Block extends Magento_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Magento_Adminhtml_Controller_Cms_Block
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('Magento_Cms::cms_block')
            ->_addBreadcrumb(__('CMS'), __('CMS'))
            ->_addBreadcrumb(__('Static Blocks'), __('Static Blocks'))
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title(__('Blocks'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new CMS block
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit CMS block
     */
    public function editAction()
    {
        $this->_title(__('Blocks'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('block_id');
        $model = Mage::getModel('Magento_Cms_Model_Block');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : __('New Block'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('cms_block', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb($id ? __('Edit Block') : __('New Block'), $id ? __('Edit Block') : __('New Block'))
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('block_id');
            $model = Mage::getModel('Magento_Cms_Model_Block')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            // init model and set data

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('The block has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('block_id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('block_id' => $this->getRequest()->getParam('block_id')));
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
        if ($id = $this->getRequest()->getParam('block_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('Magento_Cms_Model_Block');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('The block has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('block_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('We can\'t find a block to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::block');
    }
}