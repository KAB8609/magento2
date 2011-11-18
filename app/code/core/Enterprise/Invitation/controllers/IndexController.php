<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation frontend controller
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('Enterprise_Invitation_Model_Config')->isEnabledOnFront()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }

        if (!Mage::getSingleton('Mage_Customer_Model_Session')->authenticate($this)) {
            $this->getResponse()->setRedirect(Mage::helper('Mage_Customer_Helper_Data')->getLoginUrl());
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Send invitations from frontend
     *
     */
    public function sendAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $customer = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
            $invPerSend = Mage::getSingleton('Enterprise_Invitation_Model_Config')->getMaxInvitationsPerSend();
            $attempts = 0;
            $sent     = 0;
            $customerExists = 0;
            foreach ($data['email'] as $email) {
                $attempts++;
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    continue;
                }
                if ($attempts > $invPerSend) {
                    continue;
                }
                try {
                    $invitation = Mage::getModel('Enterprise_Invitation_Model_Invitation')->setData(array(
                        'email'    => $email,
                        'customer' => $customer,
                        'message'  => (isset($data['message']) ? $data['message'] : ''),
                    ))->save();
                    if ($invitation->sendInvitationEmail()) {
                        Mage::getSingleton('Mage_Customer_Model_Session')->addSuccess(Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitation for %s has been sent.', $email));
                        $sent++;
                    }
                    else {
                        throw new Exception(''); // not Mage_Core_Exception intentionally
                    }

                }
                catch (Mage_Core_Exception $e) {
                    if (Enterprise_Invitation_Model_Invitation::ERROR_CUSTOMER_EXISTS === $e->getCode()) {
                        $customerExists++;
                    }
                    else {
                        Mage::getSingleton('Mage_Customer_Model_Session')->addError($e->getMessage());
                    }
                }
                catch (Exception $e) {
                    Mage::getSingleton('Mage_Customer_Model_Session')->addError(Mage::helper('Enterprise_Invitation_Helper_Data')->__('Failed to send email to %s.', $email));
                }
            }
            if ($customerExists) {
                Mage::getSingleton('Mage_Customer_Model_Session')->addNotice(
                    Mage::helper('Enterprise_Invitation_Helper_Data')->__('%d invitation(s) were not sent, because customer accounts already exist for specified email addresses.', $customerExists)
                );
            }
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('Mage_Customer_Model_Session');
        $this->loadLayoutUpdates();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('Enterprise_Invitation_Helper_Data')->__('Send Invitations'));
        }
        $this->renderLayout();
    }

    /**
     * View invitation list in 'My Account' section
     *
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Mage_Customer_Model_Session');
        $this->loadLayoutUpdates();
        if ($block = $this->getLayout()->getBlock('invitations_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('Enterprise_Invitation_Helper_Data')->__('My Invitations'));
        }
        $this->renderLayout();
    }
}
