<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation customer account frontend controller
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Controller\Customer;

class Account extends \Magento\Customer\Controller\Account
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('createPost');

    /**
     * Predispatch custom logic
     *
     * Bypassing direct parent predispatch
     * Allowing only specific actions
     * Checking whether invitation functionality is enabled
     * Checking whether registration is allowed at all
     * No way to logged in customers
     */
    public function preDispatch()
    {
        \Magento\Core\Controller\Front\Action::preDispatch();

        if (!preg_match('/^(create|createpost)/i', $this->getRequest()->getActionName())) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
        if (!\Mage::getSingleton('Magento\Invitation\Model\Config')->isEnabledOnFront()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }

        return $this;
    }

    /**
     * Initialize invitation from request
     *
     * @return \Magento\Invitation\Model\Invitation
     */
    protected function _initInvitation()
    {
        if (!\Mage::registry('current_invitation')) {
            $invitation = \Mage::getModel('\Magento\Invitation\Model\Invitation');
            $invitation
                ->loadByInvitationCode(\Mage::helper('Magento\Core\Helper\Data')->urlDecode(
                    $this->getRequest()->getParam('invitation', false)
                ))
                ->makeSureCanBeAccepted();
            \Mage::register('current_invitation', $invitation);
        }
        return \Mage::registry('current_invitation');
    }

    /**
     * Customer register form page
     */
    public function createAction()
    {
        try {
            $invitation = $this->_initInvitation();
            $this->loadLayout();
            $this->_initLayoutMessages('\Magento\Customer\Model\Session');
            $this->renderLayout();
            return;
        }
        catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('customer/account/login');
    }

    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        try {
            $invitation = $this->_initInvitation();

            $customer = \Mage::getModel('\Magento\Customer\Model\Customer')
                ->setId(null)->setSkipConfirmationIfEmail($invitation->getEmail());
            \Mage::register('current_customer', $customer);

            if ($groupId = $invitation->getGroupId()) {
                $customer->setGroupId($groupId);
            }

            parent::createPostAction();

            if ($customerId = $customer->getId()) {
                $invitation->accept(\Mage::app()->getWebsite()->getId(), $customerId);

                $this->_eventManager->dispatch('magento_invitation_customer_accepted', array(
                   'customer' => $customer,
                   'invitation' => $invitation
                ));
            }
            return;
        }
        catch (\Magento\Core\Exception $e) {
            $_definedErrorCodes = array(
                \Magento\Invitation\Model\Invitation::ERROR_CUSTOMER_EXISTS,
                \Magento\Invitation\Model\Invitation::ERROR_INVALID_DATA
            );
            if (in_array($e->getCode(), $_definedErrorCodes)) {
                $this->_getSession()->addError($e->getMessage())
                    ->setCustomerFormData($this->getRequest()->getPost());
            } else {
                if (\Mage::helper('Magento\Customer\Helper\Data')->isRegistrationAllowed()) {
                    $this->_getSession()->addError(
                        __('Your invitation is not valid. Please create an account.')
                    );
                    $this->_redirect('customer/account/create');
                    return;
                } else {
                    $this->_getSession()->addError(
                        __('Your invitation is not valid. Please contact us at %1.', \Mage::getStoreConfig('trans_email/ident_support/email'))
                    );
                    $this->_redirect('customer/account/login');
                    return;
                }
            }
        }
        catch (\Exception $e) {
            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                ->addException($e, __('Unable to save the customer.'));
        }

        $this->_redirectError('');

        return $this;
    }

    /**
     * Make success redirect constant
     *
     * @param string $defaultUrl
     * @return \Magento\Invitation\Controller\Customer\Account
     */
    protected function _redirectSuccess($defaultUrl)
    {
        return $this->_redirect('customer/account/');
    }

    /**
     * Make failure redirect constant
     *
     * @param string $defaultUrl
     * @return \Magento\Invitation\Controller\Customer\Account
     */
    protected function _redirectError($defaultUrl)
    {
        return $this->_redirect('magento_invitation/customer_account/create',
            array('_current' => true, '_secure' => true));
    }
}
