<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profiles view/management controller
 */
class Mage_Sales_Recurring_ProfileController extends Mage_Core_Controller_Front_Action
{
    /**
     *
     * @var Mage_Customer_Model_Session
     */
    protected $_session = null;

    /**
     * Make sure customer is logged in and put it into registry
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        $this->_session = Mage::getSingleton('Mage_Customer_Model_Session');
        if (!$this->_session->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
        Mage::register('current_customer', $this->_session->getCustomer());
    }

    /**
     * Profiles listing
     */
    public function indexAction()
    {
        $this->_title(__('Recurring Billing Profiles'));
        $this->loadLayout();
        $this->_initLayoutMessages('Mage_Customer_Model_Session');
        $this->renderLayout();
    }

    /**
     * Profile main view
     */
    public function viewAction()
    {
        $this->_viewAction();
    }

    /**
     * Profile history view
     */
// TODO: implement
//    public function historyAction()
//    {
//        $this->_viewAction();
//    }

    /**
     * Profile related orders view
     */
    public function ordersAction()
    {
        $this->_viewAction();
    }

    /**
     * Profile payment gateway info view
     */
// TODO: implement
//    public function vendorAction()
//    {
//        $this->_viewAction();
//    }

    /**
     * Attempt to set profile state
     */
    public function updateStateAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();

            switch ($this->getRequest()->getParam('action')) {
                case 'cancel':
                    $profile->cancel();
                    break;
                case 'suspend':
                    $profile->suspend();
                    break;
                case 'activate':
                    $profile->activate();
                    break;
            }
            $this->_session->addSuccess(__('The profile state has been updated.'));
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_session->addError(__('We couldn\'t update the profile.'));
            Mage::logException($e);
        }
        if ($profile) {
            $this->_redirect('*/*/view', array('profile' => $profile->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Fetch an update with profile
     */
    public function updateProfileAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();
            $profile->fetchUpdate();
            if ($profile->hasDataChanges()) {
                $profile->save();
                $this->_session->addSuccess(__('The profile has been updated.'));
            } else {
                $this->_session->addNotice(__('The profile has no changes.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_session->addError(__('We couldn\'t update the profile.'));
            Mage::logException($e);
        }
        if ($profile) {
            $this->_redirect('*/*/view', array('profile' => $profile->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Generic profile view action
     */
    protected function _viewAction()
    {
        try {
            $profile = $this->_initProfile();
            $this->_title(__('Recurring Billing Profiles'))->_title(__('Profile #%s', $profile->getReferenceId()));
            $this->loadLayout();
            $this->_initLayoutMessages('Mage_Customer_Model_Session');
            $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('sales/recurring_profile/');
            }
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Instantiate current profile and put it into registry
     *
     * @return Mage_Sales_Model_Recurring_Profile
     * @throws Mage_Core_Exception
     */
    protected function _initProfile()
    {
        $profile = Mage::getModel('Mage_Sales_Model_Recurring_Profile')->load($this->getRequest()->getParam('profile'));
        if (!$profile->getId()) {
            Mage::throwException(__('We can\'t find the profile you specified.'));
        }
        Mage::register('current_recurring_profile', $profile);
        return $profile;
    }
}
