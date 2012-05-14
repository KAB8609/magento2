<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Pci observer model
 *
 * Implements hashes upgrading
 */
class Enterprise_Pci_Model_Observer
{
    const ADMIN_USER_LOCKED = 243;

    /**
     * Admin locking and password hashing upgrade logic implementation
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function adminAuthenticate($observer)
    {
        $password = $observer->getEvent()->getPassword();
        $user     = $observer->getEvent()->getUser();
        $resource = Mage::getResourceSingleton('Enterprise_Pci_Model_Resource_Admin_User');
        $authResult = $observer->getEvent()->getResult();

        // update locking information regardless whether user locked or not
        if ((!$authResult) && ($user->getId())) {
            $now = time();
            $lockThreshold = $this->getAdminLockThreshold();
            $maxFailures = (int)Mage::getStoreConfig('admin/security/lockout_failures');
            if (!($lockThreshold && $maxFailures)) {
                return;
            }
            $failuresNum = (int)$user->getFailuresNum() + 1;
            if ($firstFailureDate = $user->getFirstFailure()) {
                $firstFailureDate = new Zend_Date($firstFailureDate, Varien_Date::DATETIME_INTERNAL_FORMAT);
                $firstFailureDate = $firstFailureDate->toValue();
            }

            $updateFirstFailureDate = false;
            $updateLockExpires      = false;
            // set first failure date when this is first failure or last first failure expired
            if (1 === $failuresNum || !$firstFailureDate || (($now - $firstFailureDate) > $lockThreshold)) {
                $updateFirstFailureDate = $now;
            }
            // otherwise lock user
            elseif ($failuresNum >= $maxFailures) {
                $updateLockExpires = $now + $lockThreshold;
            }
            $resource->updateFaiure($user, $updateLockExpires, $updateFirstFailureDate);
        }

        // check whether user is locked
        if ($lockExpires = $user->getLockExpires()) {
            $lockExpires = new Zend_Date($lockExpires, Varien_Date::DATETIME_INTERNAL_FORMAT);
            $lockExpires = $lockExpires->toValue();
            if ($lockExpires > time()) {
                throw new Mage_Core_Exception(
                    Mage::helper('Enterprise_Pci_Helper_Data')->__('This account is locked.'),
                    self::ADMIN_USER_LOCKED
                );
            }
        }

        if (!$authResult) {
            return;
        }

        $resource->unlock($user->getId());

        /**
         * Check whether the latest password is expired
         * Side-effect can be when passwords were changed with different lifetime configuration settings
         */
        $latestPassword = Mage::getResourceSingleton('Enterprise_Pci_Model_Resource_Admin_User')->getLatestPassword($user->getId());
        if ($latestPassword) {
            if ($this->_isLatestPasswordExpired($latestPassword)) {
                if ($this->isPasswordChangeForced()) {
                    $message = Mage::helper('Enterprise_Pci_Helper_Data')->__('Your password has expired, you must change it now.');
                } else {
                    $myAccountUrl = Mage::getSingleton('Mage_Adminhtml_Model_Url')->getUrl('adminhtml/system_account/');
                    $message = Mage::helper('Enterprise_Pci_Helper_Data')->__('Your password has expired, please <a href="%s">change it</a>.', $myAccountUrl);
                }
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addNotice($message);
                if ($message = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getMessages()->getLastAddedMessage()) {
                    $message->setIdentifier('enterprise_pci_password_expired')->setIsSticky(true);
                    Mage::getSingleton('Mage_Backend_Model_Auth_Session')->setPciAdminUserIsPasswordExpired(true);
                }
            }
        }

        // upgrade admin password
        if (!Mage::helper('Mage_Core_Helper_Data')->getEncryptor()->validateHashByVersion($password, $user->getPassword())) {
            Mage::getModel('Mage_User_Model_User')->load($user->getId())
                ->setNewPassword($password)->setForceNewPassword(true)
                ->save();
        }
    }

    /**
     * Check if latest password is expired
     *
     * @param array $latestPassword
     * @return bool
     */
    protected function _isLatestPasswordExpired($latestPassword)
    {
        if (!isset($latestPassword['expires'])) {
            return false;
        }

        if ($this->getAdminPasswordLifetime() == 0) {
            return false;
        }

        return (int)$latestPassword['expires'] < time();
    }

    /**
     * Upgrade API key hash when api user has logged in
     *
     * @param Varien_Event_Observer $observer
     */
    public function upgradeApiKey($observer)
    {
        $apiKey = $observer->getEvent()->getApiKey();
        $model  = $observer->getEvent()->getModel();
        if (!Mage::helper('Mage_Core_Helper_Data')->getEncryptor()->validateHashByVersion($apiKey, $model->getApiKey())) {
            Mage::getModel('Mage_Api_Model_User')->load($model->getId())->setNewApiKey($apiKey)->save();
        }
    }

    /**
     * Upgrade customer password hash when customer has logged in
     *
     * @param Varien_Event_Observer $observer
     */
    public function upgradeCustomerPassword($observer)
    {
        $password = $observer->getEvent()->getPassword();
        $model    = $observer->getEvent()->getModel();
        if (!Mage::helper('Mage_Core_Helper_Data')->getEncryptor()->validateHashByVersion($password, $model->getPassword())) {
            $model->changePassword($password, false);
        }
    }

    /**
     * Harden admin password change.
     *
     * New password must be minimum 7 chars length and include alphanumeric characters
     * The password is compared to at least last 4 previous passwords to prevent setting them again
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function checkAdminPasswordChange($observer)
    {
        /* @var $user Mage_User_Model_User */
        $user = $observer->getEvent()->getObject();

        if ($user->getNewPassword()) {
            $password = $user->getNewPassword();
        } else {
            $password = $user->getPassword();
        }

        if ($password && !$user->getForceNewPassword() && $user->getId()) {
            if (Mage::helper('Mage_Core_Helper_Data')->validateHash($password, $user->getOrigData('password'))) {
                Mage::throwException(Mage::helper('Enterprise_Pci_Helper_Data')->__('This password was used earlier, try another one.'));
            }

            // check whether password was used before
            $resource     = Mage::getResourceSingleton('Enterprise_Pci_Model_Resource_Admin_User');
            $passwordHash = Mage::helper('Mage_Core_Helper_Data')->getHash($password, false);
            foreach ($resource->getOldPasswords($user) as $oldPasswordHash) {
                if ($passwordHash === $oldPasswordHash) {
                    Mage::throwException(Mage::helper('Enterprise_Pci_Helper_Data')->__('This password was used earlier, try another one.'));
                }
            }
        }
    }

    /**
     * Save new admin password
     *
     * @param Varien_Event_Observer $observer
     */
    public function trackAdminNewPassword($observer)
    {
        /* @var $user Mage_User_Model_User */
        $user = $observer->getEvent()->getObject();
        if ($user->getId()) {
            $password = $user->getNewPassword();
            $passwordLifetime = $this->getAdminPasswordLifetime();
            if ($passwordLifetime && $password && !$user->getForceNewPassword()) {
                $resource     = Mage::getResourceSingleton('Enterprise_Pci_Model_Resource_Admin_User');
                $passwordHash = Mage::helper('Mage_Core_Helper_Data')->getHash($password, false);
                $resource->trackPassword($user, $passwordHash, $passwordLifetime);
                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                        ->getMessages()
                        ->deleteMessageByIdentifier('enterprise_pci_password_expired');
                Mage::getSingleton('Mage_Backend_Model_Auth_Session')->unsPciAdminUserIsPasswordExpired();
            }
        }
    }

    /**
     * Get admin lock threshold from configuration
     *
     * @return int
     */
    public function getAdminLockThreshold()
    {
        return 60 * (int)Mage::getStoreConfig('admin/security/lockout_threshold');
    }

    /**
     * Get admin password lifetime
     *
     * @return int
     */
    public function getAdminPasswordLifetime()
    {
        return 86400 * (int)Mage::getStoreConfig('admin/security/password_lifetime');
    }

    /**
     * Force admin to change password
     *
     * @param Varien_Event_Observer $observer
     */
    public function forceAdminPasswordChange($observer)
    {
        if (!$this->isPasswordChangeForced()) {
            return;
        }
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        if (!$session->isLoggedIn()) {
            return;
        }
        $actionList = array('adminhtml_system_account_index', 'adminhtml_system_account_save',
            'adminhtml_index_logout');
        $controller = $observer->getEvent()->getControllerAction();
        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getPciAdminUserIsPasswordExpired()) {
            if (!in_array($controller->getFullActionName(), $actionList)) {
                if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('admin/system/myaccount')) {
                    $controller->getResponse()->setRedirect(Mage::getSingleton('Mage_Adminhtml_Model_Url')
                            ->getUrl('adminhtml/system_account/'));
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_POST_DISPATCH, true);
                } else {
                    /*
                     * if admin password is expired and access to 'My Account' page is denied
                     * than we need to do force logout with error message
                     */
                    Mage::getSingleton('Mage_Backend_Model_Auth_Session')->unsetAll();
                    Mage::getSingleton('Mage_Adminhtml_Model_Session')->unsetAll();
                    Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(
                        Mage::helper('Enterprise_Pci_Helper_Data')->__('Your password has expired, please contact administrator.')
                    );
                    $controller->getRequest()->setDispatched(false);
                }
            }
        }
    }

    /**
     * Check whether password change is forced
     *
     * @return bool
     */
    public function isPasswordChangeForced()
    {
        return (bool)(int)Mage::getStoreConfig('admin/security/password_is_forced');
    }
}
