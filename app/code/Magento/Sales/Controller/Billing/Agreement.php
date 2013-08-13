<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Billing agreements controller
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Controller_Billing_Agreement extends Magento_Core_Controller_Front_Action
{
    /**
     * View billing agreements
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Billing Agreements'));
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $this->renderLayout();
    }

    /**
     * Action predispatch
     *
     * Check customer authentication
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        if (!$this->_getSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * View billing agreement
     *
     */
    public function viewAction()
    {
        if (!$agreement = $this->_initAgreement()) {
            return;
        }
        $this->_title($this->__('Billing Agreements'))
            ->_title($this->__('Billing Agreement # %s', $agreement->getReferenceId()));
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('sales/billing_agreement/');
        }
        $this->renderLayout();
    }

    /**
     * Wizard start action
     *
     */
    public function startWizardAction()
    {
        $agreement = Mage::getModel('Magento_Sales_Model_Billing_Agreement');
        $paymentCode = $this->getRequest()->getParam('payment_method');
        if ($paymentCode) {
            try {
                $agreement->setStoreId(Mage::app()->getStore()->getId())
                    ->setMethodCode($paymentCode)
                    ->setReturnUrl(Mage::getUrl('*/*/returnWizard', array('payment_method' => $paymentCode)))
                    ->setCancelUrl(Mage::getUrl('*/*/cancelWizard', array('payment_method' => $paymentCode)));

                $this->_redirectUrl($agreement->initToken());
                return $this;
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('We couldn\'t start the billing agreement wizard.'));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Wizard return action
     *
     */
    public function returnWizardAction()
    {
        $agreement = Mage::getModel('Magento_Sales_Model_Billing_Agreement');
        $paymentCode = $this->getRequest()->getParam('payment_method');
        $token = $this->getRequest()->getParam('token');
        if ($token && $paymentCode) {
            try {
                $agreement->setStoreId(Mage::app()->getStore()->getId())
                    ->setToken($token)
                    ->setMethodCode($paymentCode)
                    ->setCustomer(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer())
                    ->place();
                $this->_getSession()->addSuccess(
                    $this->__('The billing agreement "%s" has been created.', $agreement->getReferenceId())
                );
                $this->_redirect('*/*/view', array('agreement' => $agreement->getId()));
                return;
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('We couldn\'t finish the billing agreement wizard.'));
            }
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Wizard cancel action
     *
     */
    public function cancelWizardAction()
    {
        $this->_redirect('*/*/index');
    }

    /**
     * Cancel action
     * Set billing agreement status to 'Canceled'
     *
     */
    public function cancelAction()
    {
        $agreement = $this->_initAgreement();
        if ($agreement && $agreement->canCancel()) {
            try {
                $agreement->cancel();
                $this->_getSession()->addNotice($this->__('The billing agreement "%s" has been canceled.', $agreement->getReferenceId()));
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('We couldn\'t cancel the billing agreement.'));
            }
        }
        $this->_redirect('*/*/view', array('_current' => true));
    }

    /**
     * Init billing agreement model from request
     *
     * @return Magento_Sales_Model_Billing_Agreement
     */
    protected function _initAgreement()
    {
        $agreementId = $this->getRequest()->getParam('agreement');
        if ($agreementId) {
            $billingAgreement = Mage::getModel('Magento_Sales_Model_Billing_Agreement')->load($agreementId);
            if (!$billingAgreement->getAgreementId()) {
                $this->_getSession()->addError($this->__('Please specify the correct billing agreement ID and try again.'));
                $this->_redirect('*/*/');
                return false;
            }
        }
        Mage::register('current_billing_agreement', $billingAgreement);
        return $billingAgreement;
    }

    /**
     * Retrieve customer session model
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session');
    }
}
