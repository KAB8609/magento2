<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_User_Adminhtml_UserController extends Mage_Backend_Controller_ActionAbstract
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Mage_User::system_acl_users')
            ->_addBreadcrumb(__('System'), __('System'))
            ->_addBreadcrumb(__('Permissions'), __('Permissions'))
            ->_addBreadcrumb(__('Users'), __('Users'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title(__('Users'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title(__('Users'));

        $userId = $this->getRequest()->getParam('user_id');
        $model = Mage::getModel('Mage_User_Model_User');

        if ($userId) {
            $model->load($userId);
            if (! $model->getId()) {
                $this->_session->addError(__('This user no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            $model->setInterfaceLocale(Mage_Core_Model_LocaleInterface::DEFAULT_LOCALE);
        }

        $this->_title($model->getId() ? $model->getName() : __('New User'));

        // Restore previously entered form data from session
        $data = $this->_session->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('permissions_user', $model);

        if (isset($userId)) {
            $breadcrumb = __('Edit User');
        } else {
            $breadcrumb = __('New User');
        }
        $this->_initAction()->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->renderLayout();
    }

    public function saveAction()
    {
        $userId = (int)$this->getRequest()->getParam('user_id');
        $data = $this->getRequest()->getPost();
        if (!$data) {
            $this->_redirect('*/*/');
            return;
        }
        /** @var $model Mage_User_Model_User */
        $model = $this->_objectManager->create('Mage_User_Model_User')->load($userId);
        if ($userId && $model->isObjectNew()) {
            $this->_getSession()->addError(__('This user no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $model->setData($this->_getAdminUserData($data));
        $uRoles = $this->getRequest()->getParam('roles', array());
        if (count($uRoles)) {
            $model->setRoleId($uRoles[0]);
        }

        $currentUser = $this->_objectManager->get('Mage_Backend_Model_Auth_Session')->getUser();
        if ($userId == $currentUser->getId()
            && $this->_objectManager->get('Mage_Core_Model_Locale_Validator')->isValid($data['interface_locale'])
        ) {
            $this->_objectManager->get('Mage_Backend_Model_Locale_Manager')
                ->switchBackendInterfaceLocale($data['interface_locale']);
        }

        try {
            $model->save();
            $this->_getSession()->addSuccess(__('You saved the user.'));
            $this->_getSession()->setUserData(false);
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addMessages($e->getMessages());
            $this->_getSession()->setUserData($data);
            $this->_redirect('*/*/edit', array('_current' => true));
        }
    }

    /**
     * Retrieve well-formed admin user data from the form input
     *
     * @param array $data
     * @return array
     */
    protected function _getAdminUserData(array $data)
    {
        if (isset($data['password']) && $data['password'] === '') {
            unset($data['password']);
        }
        if (isset($data['password_confirmation']) && $data['password_confirmation'] === '') {
            unset($data['password_confirmation']);
        }
        return $data;
    }

    public function deleteAction()
    {
        $currentUser = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser();

        if ($userId = $this->getRequest()->getParam('user_id')) {
            if ( $currentUser->getId() == $userId ) {
                $this->_session->addError(
                    __('You cannot delete your own account.')
                );
                $this->_redirect('*/*/edit', array('user_id' => $userId));
                return;
            }
            try {
                $model = Mage::getModel('Mage_User_Model_User');
                $model->setId($userId);
                $model->delete();
                $this->_session->addSuccess(__('You deleted the user.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                $this->_session->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('user_id' => $this->getRequest()->getParam('user_id')));
                return;
            }
        }
        $this->_session->addError(__('We can\'t find a user to delete.'));
        $this->_redirect('*/*/');
    }

    public function rolesGridAction()
    {
        $userId = $this->getRequest()->getParam('user_id');
        $model = Mage::getModel('Mage_User_Model_User');

        if ($userId) {
            $model->load($userId);
        }
        Mage::register('permissions_user', $model);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function roleGridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_User::acl_users');
    }

}
