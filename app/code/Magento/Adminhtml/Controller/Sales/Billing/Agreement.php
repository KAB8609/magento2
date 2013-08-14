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
 * Adminhtml billing agreement controller
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Sales_Billing_Agreement extends Magento_Adminhtml_Controller_Action
{
    /**
     * Billing agreements
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Billing Agreements'));

        $this->loadLayout()
            ->_setActiveMenu('Magento_Sales::sales_billing_agreement')
            ->renderLayout();
    }

    /**
     * Ajax action for billing agreements
     *
     */
    public function gridAction()
    {
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     * View billing agreement action
     *
     */
    public function viewAction()
    {
        $agreementModel = $this->_initBillingAgreement();

        if ($agreementModel) {
            $this->_title($this->__('Billing Agreements'))
                ->_title(sprintf("#%s", $agreementModel->getReferenceId()));

            $this->loadLayout()
                ->_setActiveMenu('Magento_Sales::sales_billing_agreement')
                ->renderLayout();
            return;
        }

        $this->_redirect('*/*/');
        return;
    }

    /**
     * Related orders ajax action
     *
     */
    public function ordersGridAction()
    {
        $this->_initBillingAgreement();
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     * Cutomer billing agreements ajax action
     *
     */
    public function customerGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     * Cancel billing agreement action
     *
     */
    public function cancelAction()
    {
        $agreementModel = $this->_initBillingAgreement();

        if ($agreementModel && $agreementModel->canCancel()) {
            try {
                $agreementModel->cancel();
                $this->_getSession()->addSuccess($this->__('You canceled the billing agreement.'));
                $this->_redirect('*/*/view', array('_current' => true));
                return;
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('We could not cancel the billing agreement.'));
                Mage::logException($e);
            }
            $this->_redirect('*/*/view', array('_current' => true));
        }
        return $this->_redirect('*/*/');
    }

    /**
     * Delete billing agreement action
     */
    public function deleteAction()
    {
        $agreementModel = $this->_initBillingAgreement();

        if ($agreementModel) {
            try {
                $agreementModel->delete();
                $this->_getSession()->addSuccess($this->__('You deleted the billing agreement.'));
                $this->_redirect('*/*/');
                return;
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('We could not delete the billing agreement.'));
                Mage::logException($e);
            }
            $this->_redirect('*/*/view', array('_current' => true));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Initialize billing agreement by ID specified in request
     *
     * @return Magento_Sales_Model_Billing_Agreement | false
     */
    protected function _initBillingAgreement()
    {
        $agreementId = $this->getRequest()->getParam('agreement');
        $agreementModel = Mage::getModel('Magento_Sales_Model_Billing_Agreement')->load($agreementId);

        if (!$agreementModel->getId()) {
            $this->_getSession()->addError($this->__('Please specify the correct billing agreement ID and try again.'));
            return false;
        }

        Mage::register('current_billing_agreement', $agreementModel);
        return $agreementModel;
    }

    /**
     * Initialize customer by ID specified in request
     *
     * @return Magento_Adminhtml_Controller_Sales_Billing_Agreement
     */
    protected function _initCustomer()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        $customer = Mage::getModel('Magento_Customer_Model_Customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'index':
            case 'grid' :
            case 'view' :
                return $this->_authorization->isAllowed('Magento_Sales::billing_agreement_actions_view');
                break;
            case 'cancel':
            case 'delete':
                return $this->_authorization->isAllowed('Magento_Sales::actions_manage');
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Sales::billing_agreement');
                break;
        }
    }
}
