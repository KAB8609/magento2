<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend Auth model
 */
namespace Magento\Backend\Model;

class Auth
{
    /**
     * @var \Magento\Backend\Model\Auth\StorageInterface
     */
    protected $_authStorage;

    /**
     * @var \Magento\Backend\Model\Auth\Credential\StorageInterface
     */
    protected $_credentialStorage;

    /**
     * Backend data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @var \Magento\Core\Model\Factory
     */
    protected $_modelFactory;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Backend\Model\Auth\StorageInterface $authStorage
     * @param \Magento\Backend\Model\Auth\Credential\StorageInterface $credentialStorage
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\Core\Model\Factory $modelFactory
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Backend\Model\Auth\StorageInterface $authStorage,
        \Magento\Backend\Model\Auth\Credential\StorageInterface $credentialStorage,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\Core\Model\Factory $modelFactory
    ) {
        $this->_eventManager = $eventManager;
        $this->_backendData = $backendData;
        $this->_authStorage = $authStorage;
        $this->_credentialStorage = $credentialStorage;
        $this->_coreConfig = $coreConfig;
        $this->_modelFactory = $modelFactory;
    }

    /**
     * Set auth storage if it is instance of \Magento\Backend\Model\Auth\StorageInterface
     *
     * @param \Magento\Backend\Model\Auth\StorageInterface $storage
     * @return \Magento\Backend\Model\Auth
     * @throw \Magento\Backend\Model\Auth\Exception if $storage is not correct
     */
    public function setAuthStorage($storage)
    {
        if (!($storage instanceof \Magento\Backend\Model\Auth\StorageInterface)) {
            self::throwException('Authentication storage is incorrect.');
        }
        $this->_authStorage = $storage;
        return $this;
    }

    /**
     * Return auth storage.
     * If auth storage was not defined outside - returns default object of auth storage
     *
     * @return \Magento\Backend\Model\Auth\StorageInterface
     */
    public function getAuthStorage()
    {
        return $this->_authStorage;
    }

    /**
     * Return current (successfully authenticated) user,
     * an instance of \Magento\Backend\Model\Auth\Credential\StorageInterface
     *
     * @return \Magento\Backend\Model\Auth\Credential\StorageInterface
     */
    public function getUser()
    {
        return $this->getAuthStorage()->getUser();
    }

    /**
     * Initialize credential storage from configuration
     *
     * @return void
     */
    protected function _initCredentialStorage()
    {
        $this->_credentialStorage = $this->_modelFactory->create(
            'Magento\Backend\Model\Auth\Credential\StorageInterface'
        );
    }

    /**
     * Return credential storage object
     *
     * @return null | \Magento\Backend\Model\Auth\Credential\StorageInterface
     */
    public function getCredentialStorage()
    {
        return $this->_credentialStorage;
    }

    /**
     * Perform login process
     *
     * @param string $username
     * @param string $password
     * @throws \Exception|\Magento\Backend\Model\Auth\Plugin\Exception
     */
    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            self::throwException(
                __('Please correct the user name or password.')
            );
        }

        try {
            $this->_initCredentialStorage();
            $this->getCredentialStorage()->login($username, $password);
            if ($this->getCredentialStorage()->getId()) {

                $this->getAuthStorage()->setUser($this->getCredentialStorage());
                $this->getAuthStorage()->processLogin();

                $this->_eventManager
                    ->dispatch('backend_auth_user_login_success', array('user' => $this->getCredentialStorage()));
            }

            if (!$this->getAuthStorage()->getUser()) {
                self::throwException(
                    __('Please correct the user name or password.')
                );
            }

        } catch (\Magento\Backend\Model\Auth\Plugin\Exception $e) {
            $this->_eventManager
                ->dispatch('backend_auth_user_login_failed', array('user_name' => $username, 'exception' => $e));
            throw $e;
        } catch (\Magento\Core\Exception $e) {
            $this->_eventManager
                ->dispatch('backend_auth_user_login_failed', array('user_name' => $username, 'exception' => $e));
            self::throwException(
                __('Please correct the user name or password.')
            );
        }
    }

    /**
     * Perform logout process
     *
     * @return void
     */
    public function logout()
    {
        $this->getAuthStorage()->processLogout();
    }

    /**
     * Check if current user is logged in
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return $this->getAuthStorage()->isLoggedIn();
    }

    /**
     * Throws specific Backend Authentication \Exception
     *
     * @static
     * @param string $msg
     * @param string $code
     * @throws \Magento\Backend\Model\Auth\Exception
     */
    public static function throwException($msg = null, $code = null)
    {
        if (is_null($msg)) {
            $msg = __('Authentication error occurred.');
        }
        throw new \Magento\Backend\Model\Auth\Exception($msg, $code);
    }
}
