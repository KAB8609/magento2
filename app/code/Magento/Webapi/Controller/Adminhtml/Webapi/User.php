<?php
/**
 * Controller for web API users management in Magento admin panel.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Adminhtml\Webapi;

class User extends \Magento\Backend\App\AbstractAction
{
    /**
     * @var \Magento\Core\Model\Validator\Factory
     */
    protected $_validatorFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Validator\Factory $validatorFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Validator\Factory $validatorFactory
    ) {
        $this->_validatorFactory = $validatorFactory;
        parent::__construct($context);
    }

    /**
     * Initialize breadcrumbs.
     *
     * @return \Magento\Webapi\Controller\Adminhtml\Webapi\User
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Webapi::system_api_webapi_users')
            ->_addBreadcrumb(
                __('Web Services'),
                __('Web Services')
            )
            ->_addBreadcrumb(
                __('API Users'),
                __('API Users')
            );

        return $this;
    }

    /**
     * Show web API users grid.
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_title->add(__('API Users'));

        $this->_view->renderLayout();
    }

    /**
     * Create New Web API user.
     */
    public function newAction()
    {
        $this->getRequest()->setParam('user_id', null);
        $this->_forward('edit');
    }

    /**
     * Edit Web API user.
     */
    public function editAction()
    {
        $this->_initAction();
        $this->_title->add(__('API Users'));

        $userId = (int)$this->getRequest()->getParam('user_id');
        $user = $this->_loadApiUser($userId);
        if (!$user) {
            return;
        }

        // Update title and breadcrumb record.
        $actionTitle = $user->getId()
            ? $this->_objectManager->get('Magento\Escaper')->escapeHtml($user->getApiKey())
            : __('New API User');
        $this->_title->add($actionTitle);
        $this->_addBreadcrumb($actionTitle, $actionTitle);

        // Restore previously entered form data from session.
        $data = $this->_getSession()->getWebapiUserData(true);
        if (!empty($data)) {
            $user->setData($data);
        }

        /** @var \Magento\Webapi\Block\Adminhtml\User\Edit $editBlock */
        $editBlock = $this->_view->getLayout()->getBlock('webapi.user.edit');
        if ($editBlock) {
            $editBlock->setApiUser($user);
        }
        /** @var \Magento\Webapi\Block\Adminhtml\User\Edit\Tabs $tabsBlock */
        $tabsBlock = $this->_view->getLayout()->getBlock('webapi.user.edit.tabs');
        if ($tabsBlock) {
            $tabsBlock->setApiUser($user);
        }

        $this->_view->renderLayout();
    }

    /**
     * Save Web API user.
     */
    public function saveAction()
    {
        $userId = (int)$this->getRequest()->getPost('user_id');
        $data = $this->getRequest()->getPost();
        $redirectBack = false;
        if ($data) {
            $user = $this->_loadApiUser($userId);
            if (!$user) {
                return;
            }

            $user->setData($data);
            try {
                $this->_validateUserData($user);
                $user->save();
                $userId = $user->getId();

                $this->_getSession()->setWebapiUserData(null);
                $this->messageManager->addSuccess(__('The API user has been saved.'));
                $redirectBack = $this->getRequest()->has('back');
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->setWebapiUserData($data);
                $this->messageManager->addError($e->getMessage());
                $redirectBack = true;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->_getSession()->setWebapiUserData($data);
                $this->messageManager->addError($e->getMessage());
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect('adminhtml/*/edit', array('user_id' => $userId));
        } else {
            $this->_redirect('adminhtml/*/');
        }
    }

    /**
     * Delete user.
     */
    public function deleteAction()
    {
        $userId = (int)$this->getRequest()->getParam('user_id');
        if ($userId) {
            $user = $this->_loadApiUser($userId);
            if (!$user) {
                return;
            }
            try {
                $user->delete();

                $this->messageManager->addSuccess(
                    __('The API user has been deleted.')
                );
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('user_id' => $userId));
                return;
            }
        }
        $this->messageManager->addError(
            __('Unable to find a user to be deleted.')
        );
        $this->_redirect('adminhtml/*/');
    }

    /**
     * AJAX Web API users grid.
     */
    public function gridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Web API user roles grid.
     */
    public function rolesgridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Check ACL.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Webapi::webapi_users');
    }

    /**
     * Validate Web API user data.
     *
     * @param \Magento\Webapi\Model\Acl\User $user
     * @throws \Magento\Validator\ValidatorException
     */
    protected function _validateUserData($user)
    {
        $group = $user->isObjectNew() ? 'create' : 'update';
        $validator = $this->_validatorFactory->createValidator('api_user', $group);
        if (!$validator->isValid($user)) {
            throw new \Magento\Validator\ValidatorException($validator->getMessages());
        }
    }

    /**
     * Load Web API user.
     *
     * @param int $userId
     * @return bool|\Magento\Webapi\Model\Acl\User
     */
    protected function _loadApiUser($userId)
    {
        /** @var \Magento\Webapi\Model\Acl\User $user */
        $user = $this->_objectManager->create('Magento\Webapi\Model\Acl\User')->load($userId);
        if (!$user->getId() && $userId) {
            $this->messageManager->addError(
                __('This user no longer exists.')
            );
            $this->_redirect('adminhtml/*/');
            return false;
        }
        return $user;
    }
}
