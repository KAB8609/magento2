<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Controller\Adminhtml;

class Index extends \Magento\Backend\Controller\Adminhtml\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * Invitation Factory
     *
     * @var \Magento\Invitation\Model\InvitationFactory
     */
    protected $_invitationFactory;

    /**
     * Store Manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Invitation Config
     *
     * @var \Magento\Invitation\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Invitation\Model\InvitationFactory $invitationFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Invitation\Model\Config $config
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Invitation\Model\InvitationFactory $invitationFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Invitation\Model\Config $config
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->_invitationFactory = $invitationFactory;
        $this->_storeManager = $storeManager;
        $this->_config = $config;
    }

    /**
     * Invitation list
     */
    public function indexAction()
    {
        $this->_title(__('Invitations'));
        $this->loadLayout()->_setActiveMenu('Magento_Invitation::customer_magento_invitation');
        $this->renderLayout();
    }

    /**
     * Init invitation model by request
     *
     * @return \Magento\Invitation\Model\Invitation
     * @throws \Magento\Core\Exception
     */
    protected function _initInvitation()
    {
        $this->_title(__('Invitations'));

        $invitation =  $this->_invitationFactory->create()->load($this->getRequest()->getParam('id'));
        if (!$invitation->getId()) {
            throw new \Magento\Core\Exception(__("We couldn't find this invitation."));
        }
        $this->_coreRegistry->register('current_invitation', $invitation);

        return $invitation;
    }

    /**
     * Invitation view action
     */
    public function viewAction()
    {
        try {
            $this->_initInvitation();
            $this->loadLayout()->_setActiveMenu('Magento_Invitation::customer_magento_invitation');
            $this->renderLayout();
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('invitations/*/');
        }
    }

    /**
     * Create new invitatoin form
     */
    public function newAction()
    {
        $this->loadLayout()->_setActiveMenu('Magento_Invitation::customer_magento_invitation');
        $this->renderLayout();
    }

    /**
     * Create & send new invitations
     */
    public function saveAction()
    {
        try {
            // parse POST data
            if (!$this->getRequest()->isPost()) {
                $this->_redirect('invitations/*/');
                return;
            }
            $this->_getSession()->setInvitationFormData($this->getRequest()->getPost());
            $emails = preg_split('/\s+/s', $this->getRequest()->getParam('email'));
            foreach ($emails as $key => $email) {
                $email = trim($email);
                if (empty($email)) {
                    unset($emails[$key]);
                } else {
                    $emails[$key] = $email;
                }
            }
            if (empty($emails)) {
                throw new \Magento\Core\Exception(__('Please specify at least one email address.'));
            }
            if ($this->_storeManager->hasSingleStore()) {
                $storeId = $this->_storeManager->getStore(true)->getId();
            } else {
                $storeId = $this->getRequest()->getParam('store_id');
            }

            // try to send invitation(s)
            $sentCount   = 0;
            $failedCount = 0;
            $customerExistsCount = 0;
            foreach ($emails as $key => $email) {
                try {
                    $invitation =  $this->_invitationFactory->create()->setData(array(
                        'email'    => $email,
                        'store_id' => $storeId,
                        'message'  => $this->getRequest()->getParam('message'),
                        'group_id' => $this->getRequest()->getParam('group_id'),
                    ))->save();
                    if ($invitation->sendInvitationEmail()) {
                        $sentCount++;
                    } else {
                        $failedCount++;
                    }
                } catch (\Magento\Core\Exception $e) {
                    if ($e->getCode()) {
                        $failedCount++;
                        if ($e->getCode() == \Magento\Invitation\Model\Invitation::ERROR_CUSTOMER_EXISTS) {
                            $customerExistsCount++;
                        }
                    } else {
                        throw $e;
                    }
                }
            }
            if ($sentCount) {
                $this->_getSession()->addSuccess(__('We sent %1 invitation(s).', $sentCount));
            }
            if ($failedCount) {
                $this->_getSession()->addError(__('Something went wrong sending %1 of %2 invitations.', $failedCount, count($emails)));
            }
            if ($customerExistsCount) {
                $this->_getSession()->addNotice(__('%1 invitation(s) were not sent, because customer accounts already exist for specified email addresses.', $customerExistsCount));
            }
            $this->_getSession()->unsInvitationFormData();
            $this->_redirect('invitations/*/');
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('invitations/*/new');
    }

    /**
     * Edit invitation's information
     */
    public function saveInvitationAction()
    {
        try {
            $invitation = $this->_initInvitation();

            if ($this->getRequest()->isPost()) {
                $email = $this->getRequest()->getParam('email');

                $invitation->setMessage($this->getRequest()->getParam('message'))
                    ->setEmail($email);

                $result = $invitation->validate();
                //checking if there was validation
                if (is_array($result) && !empty($result)) {
                    foreach ($result as $message) {
                        $this->_getSession()->addError($message);
                    }
                    $this->_redirect('invitations/*/view', array('_current' => true));
                    return $this;
                }

                //If there was no validation errors trying to save
                $invitation->save();

                $this->_getSession()->addSuccess(__('The invitation has been saved.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('invitations/*/view', array('_current' => true));
    }

    /**
     * Action for mass-resending invitations
     *
     * @throws \Magento\Core\Exception
     */
    public function massResendAction()
    {
        try {
            $invitationsPost = $this->getRequest()->getParam('invitations', array());
            if (empty($invitationsPost) || !is_array($invitationsPost)) {
                throw new \Magento\Core\Exception(__('Please select invitations.'));
            }
            $collection =  $this->_invitationFactory->create()->getCollection()
                ->addFieldToFilter('invitation_id', array('in' => $invitationsPost))
                ->addCanBeSentFilter();
            $found = 0;
            $sent  = 0;
            $customerExists = 0;
            foreach ($collection as $invitation) {
                try {
                    $invitation->makeSureCanBeSent();
                    $found++;
                    if ($invitation->sendInvitationEmail()) {
                        $sent++;
                    }
                } catch (\Magento\Core\Exception $e) {
                    // jam all exceptions with codes
                    if (!$e->getCode()) {
                        throw $e;
                    }
                    // close irrelevant invitations
                    if ($e->getCode() === \Magento\Invitation\Model\Invitation::ERROR_CUSTOMER_EXISTS) {
                        $customerExists++;
                        $invitation->cancel();
                    }
                }
            }
            if (!$found) {
                $this->_getSession()->addError(__('No invitations have been resent.'));
            }
            if ($sent) {
                $this->_getSession()->addSuccess(__('We sent %1 of %2 invitations.', $sent, $found));
            }
            $failed = $found - $sent;
            if ($failed) {
                $this->_getSession()->addError(__('Something went wrong sending %1 invitations.', $failed));
            }
            if ($customerExists) {
                $this->_getSession()->addNotice(
                    __('We discarded %1 invitation(s) addressed to current customers.', $customerExists)
                );
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('invitations/*/');
    }

    /**
     * Action for mass-cancelling invitations
     *
     * @throws \Magento\Core\Exception
     */
    public function massCancelAction()
    {
        try {
            $invitationsPost = $this->getRequest()->getParam('invitations', array());
            if (empty($invitationsPost) || !is_array($invitationsPost)) {
                throw new \Magento\Core\Exception(__('Please select invitations.'));
            }
            $collection =  $this->_invitationFactory->create()->getCollection()
                ->addFieldToFilter('invitation_id', array('in' => $invitationsPost))
                ->addCanBeCanceledFilter();
            $found     = 0;
            $cancelled = 0;
            foreach ($collection as $invitation) {
                try {
                    $found++;
                    if ($invitation->canBeCanceled()) {
                        $invitation->cancel();
                        $cancelled++;
                    }
                } catch (\Magento\Core\Exception $e) {
                    // jam all exceptions with codes
                    if (!$e->getCode()) {
                        throw $e;
                    }
                }
            }
            if ($cancelled) {
                $this->_getSession()->addSuccess(__('We discarded %1 of %2 invitations.', $cancelled, $found));
            }
            $failed = $found - $cancelled;
            if ($failed) {
                $this->_getSession()->addNotice(__('We skipped %1 of the selected invitations.', $failed));
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('invitations/*/');
    }

    /**
     * Acl admin user check
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_config->isEnabled()
            && $this->_authorization->isAllowed('Magento_Invitation::magento_invitation');
    }
}
