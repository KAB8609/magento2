<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Banners list
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Banners'));

        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_Banner::cms_enterprise_banner');
        $this->renderLayout();
    }

    /**
     * Create new banner
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit action
     *
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $id) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_Banner_Helper_Data')->__('This banner no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Banner'));

        $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_Banner::cms_enterprise_banner');
        $this->_addBreadcrumb($id ? Mage::helper('Enterprise_Banner_Helper_Data')->__('Edit Banner') : Mage::helper('Enterprise_Banner_Helper_Data')->__('New Banner'),
                              $id ? Mage::helper('Enterprise_Banner_Helper_Data')->__('Edit Banner') : Mage::helper('Enterprise_Banner_Helper_Data')->__('New Banner'))
             ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('id');
            $model = $this->_initBanner();
            if (!$model->getId() && $id) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_Banner_Helper_Data')->__('This banner no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            //Filter disallowed data
            $currentStores = array_keys(Mage::app()->getStores(true));
            if (isset($data['store_contents_not_use'])) {
                $data['store_contents_not_use'] = array_intersect($data['store_contents_not_use'], $currentStores);
            }
            if (isset($data['store_contents'])) {
                $data['store_contents'] = array_intersect_key($data['store_contents'], array_flip($currentStores));
            }

            // prepare post data
            if (isset($data['banner_catalog_rules'])) {
                $related = Mage::helper('Mage_Adminhtml_Helper_Js')->decodeGridSerializedInput($data['banner_catalog_rules']);
                foreach ($related as $_key => $_rid) {
                    $related[$_key] = (int)$_rid;
                }
                $data['banner_catalog_rules'] = $related;
            }
            if (isset($data['banner_sales_rules'])) {
                $related = Mage::helper('Mage_Adminhtml_Helper_Js')->decodeGridSerializedInput($data['banner_sales_rules']);
                foreach ($related as $_key => $_rid) {
                    $related[$_key] = (int)$_rid;
                }
                $data['banner_sales_rules'] = $related;
            }

            // save model
            try {
                if (!empty($data)) {
                    $model->addData($data);
                    Mage::getSingleton('Mage_Adminhtml_Model_Session')->setFormData($data);
                }
                $model->save();
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->setFormData(false);
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Enterprise_Banner_Helper_Data')->__('The banner has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('Enterprise_Banner_Helper_Data')->__('Unable to save the banner.'));
                $redirectBack = true;
                Mage::logException($e);
            }
            if ($redirectBack) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     *
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('Enterprise_Banner_Model_Banner');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Enterprise_Banner_Helper_Data')->__('The banner has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('Enterprise_Banner_Helper_Data')->__('An error occurred while deleting banner data. Please review log and try again.'));
                Mage::logException($e);
                // save data in session
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_Banner_Helper_Data')->__('Unable to find a banner to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Delete specified banners using grid massaction
     *
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('banner');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select banner(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('Enterprise_Banner_Model_Banner')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('Enterprise_Banner_Helper_Data')->__('An error occurred while mass deleting banners. Please review log and try again.'));
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }


    /**
     * Load Banner from request
     *
     * @param string $idFieldName
     * @return Enterprise_Banner_Model_Banner $model
     */
    protected function _initBanner($idFieldName = 'banner_id')
    {
        $this->_title($this->__('Banners'));

        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('Enterprise_Banner_Model_Banner');
        if ($id) {
            $model->load($id);
        }
        if (!Mage::registry('current_banner')) {
            Mage::register('current_banner', $model);
        }
        return $model;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Enterprise_Banner::enterprise_banner');
    }

    /**
     * Render Banner grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Banner sales rule grid action on promotions tab
     * Load banner by ID from post data
     * Register banner model
     *
     */
    public function salesRuleGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $id) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_Banner_Helper_Data')->__('This banner no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->getLayout()
            ->getBlock('banner_salesrule_grid')
            ->setSelectedSalesRules($this->getRequest()->getPost('selected_salesrules'));
        $this->renderLayout();
    }

    /**
     * Banner catalog rule grid action on promotions tab
     * Load banner by ID from post data
     * Register banner model
     *
     */
    public function catalogRuleGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $id) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_Banner_Helper_Data')->__('This banner no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->getLayout()
            ->getBlock('banner_catalogrule_grid')
            ->setSelectedCatalogRules($this->getRequest()->getPost('selected_catalogrules'));
        $this->renderLayout();
    }

    /**
     * Banner binding tab grid action on sales rule
     *
     */
    public function salesRuleBannersGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('Mage_SalesRule_Model_Rule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_SalesRule_Helper_Data')->__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }
        if (!Mage::registry('current_promo_quote_rule')) {
            Mage::register('current_promo_quote_rule', $model);
        }
        $this->loadLayout();
        $this->getLayout()
            ->getBlock('related_salesrule_banners_grid')
            ->setSelectedSalesruleBanners($this->getRequest()->getPost('selected_salesrule_banners'));
        $this->renderLayout();
    }

   /**
     * Banner binding tab grid action on catalog rule
     *
     */
    public function catalogRuleBannersGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('Mage_CatalogRule_Model_Rule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_CatalogRule_Helper_Data')->__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }
        if (!Mage::registry('current_promo_catalog_rule')) {
            Mage::register('current_promo_catalog_rule', $model);
        }
        $this->loadLayout();
        $this->getLayout()
            ->getBlock('related_catalogrule_banners_grid')
            ->setSelectedCatalogruleBanners($this->getRequest()->getPost('selected_catalogrule_banners'));
        $this->renderLayout();
    }
}
