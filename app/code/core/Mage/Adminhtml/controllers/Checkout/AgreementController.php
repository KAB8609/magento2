<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax rule controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Checkout_AgreementController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Terms and Conditions'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Checkout_Agreement'))
            ->renderLayout();
        return $this;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Terms and Conditions'));

        $id  = $this->getRequest()->getParam('id');
        $agreementModel  = Mage::getModel('Mage_Checkout_Model_Agreement');
        $hlp = Mage::helper('Mage_Checkout_Helper_Data');
        if ($id) {
            $agreementModel->load($id);
            if (!$agreementModel->getId()) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($hlp->__('This condition no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($agreementModel->getId() ? $agreementModel->getName() : $this->__('New Condition'));

        $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getAgreementData(true);
        if (!empty($data)) {
            $agreementModel->setData($data);
        }

        Mage::register('checkout_agreement', $agreementModel);

        $this->_initAction()
            ->_addBreadcrumb($id ? $hlp->__('Edit Condition') :  $hlp->__('New Condition'), $id ?  $hlp->__('Edit Condition') :  $hlp->__('New Condition'))
            ->_addContent(
                $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Checkout_Agreement_Edit')
                    ->setData('action', $this->getUrl('*/*/save'))
            )
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('Mage_Checkout_Model_Agreement');
            $model->setData($postData);

            try {
                $model->save();

                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Mage_Checkout_Helper_Data')->__('The condition has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Checkout_Helper_Data')->__('An error occurred while saving this condition.'));
            }

            Mage::getSingleton('Mage_Adminhtml_Model_Session')->setAgreementData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getSingleton('Mage_Checkout_Model_Agreement')
            ->load($id);
        if (!$model->getId()) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Checkout_Helper_Data')->__('This condition no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();

            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Mage_Checkout_Helper_Data')->__('The condition has been deleted'));
            $this->_redirect('*/*/');

            return;
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Checkout_Helper_Data')->__('An error occurred while deleting this condition.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/checkoutagreement')
            ->_addBreadcrumb(Mage::helper('Mage_Checkout_Helper_Data')->__('Sales'), Mage::helper('Mage_Checkout_Helper_Data')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('Mage_Checkout_Helper_Data')->__('Checkout Conditions'), Mage::helper('Mage_Checkout_Helper_Data')->__('Checkout Terms and Conditions'))
        ;
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('sales/checkoutagreement');
    }
}
