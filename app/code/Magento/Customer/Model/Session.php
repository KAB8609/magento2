<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer session model
 */
namespace Magento\Customer\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * Customer object
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * Flag with customer id validations result
     *
     * @var bool
     */
    protected $_isCustomerIdChecked = null;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData = null;

    /**
     * Core url
     *
     * @var \Magento\Core\Helper\Url
     */
    protected $_coreUrl = null;

    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $_configShare;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Customer\Model\Resource\Customer
     */
    protected $_customerResource;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Core\Model\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param \Magento\Customer\Model\Config\Share $configShare
     * @param \Magento\Core\Helper\Url $coreUrl
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\Customer\Model\Resource\Customer $customerResource
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Core\Model\UrlFactory $urlFactory
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Core\Helper\Url $coreUrl,
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Core\Model\Session $session,
        \Magento\Customer\Model\Resource\Customer $customerResource,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Core\Model\UrlFactory $urlFactory,
        array $data = array(),
        $sessionName = null
    ) {
        $this->_coreUrl = $coreUrl;
        $this->_customerData = $customerData;
        $this->_configShare = $configShare;
        $this->_session = $session;
        $this->_customerResource = $customerResource;
        $this->_customerFactory = $customerFactory;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context, $data);
        $namespace = 'customer';
        if ($configShare->isWebsiteScope()) {
            $namespace .= '_' . ($this->_storeManager->getWebsite()->getCode());
        }

        $this->init($namespace, $sessionName);
        $this->_eventManager->dispatch('customer_session_init', array('customer_session' => $this));
    }

    /**
     * Retrieve customer sharing configuration model
     *
     * @return \Magento\Customer\Model\Config\Share
     */
    public function getCustomerConfigShare()
    {
        return $this->_configShare;
    }

    /**
     * Set customer object and setting customer id in session
     *
     * @param   \Magento\Customer\Model\Customer $customer
     * @return  \Magento\Customer\Model\Session
     */
    public function setCustomer(\Magento\Customer\Model\Customer $customer)
    {
        // check if customer is not confirmed
        if ($customer->isConfirmationRequired()) {
            if ($customer->getConfirmation()) {
                return $this->_logout();
            }
        }
        $this->_customer = $customer;
        $this->setId($customer->getId());
        // save customer as confirmed, if it is not
        if ((!$customer->isConfirmationRequired()) && $customer->getConfirmation()) {
            $customer->setConfirmation(null)->save();
            $customer->setIsJustConfirmed(true);
        }
        return $this;
    }

    /**
     * Retrieve customer model object
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if ($this->_customer instanceof \Magento\Customer\Model\Customer) {
            return $this->_customer;
        }

        $customer = $this->_createCustomer()->setWebsiteId($this->_storeManager->getStore()->getWebsiteId());
        if ($this->getId()) {
            $customer->load($this->getId());
        }

        $this->setCustomer($customer);
        return $this->_customer;
    }

    /**
     * Set customer id
     *
     * @param int|null $id
     * @return \Magento\Customer\Model\Session
     */
    public function setCustomerId($id)
    {
        $this->setData('customer_id', $id);
        return $this;
    }

    /**
     * Retrieve customer id from current session
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        if ($this->getData('customer_id')) {
            return $this->getData('customer_id');
        }
        return ($this->isLoggedIn()) ? $this->getId() : null;
    }

    /**
     * Set customer group id
     *
     * @param int|null $id
     * @return \Magento\Customer\Model\Session
     */
    public function setCustomerGroupId($id)
    {
        $this->setData('customer_group_id', $id);
        return $this;
    }

    /**
     * Get customer group id
     * If customer is not logged in system, 'not logged in' group id will be returned
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if ($this->getData('customer_group_id')) {
            return $this->getData('customer_group_id');
        }
        if ($this->isLoggedIn() && $this->getCustomer()) {
            return $this->getCustomer()->getGroupId();
        }
        return \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool)$this->getId() && (bool)$this->checkCustomerId($this->getId());
    }

    /**
     * Check exists customer (light check)
     *
     * @param int $customerId
     * @return bool
     */
    public function checkCustomerId($customerId)
    {
        if ($this->_isCustomerIdChecked === null) {
            $this->_isCustomerIdChecked = $this->_customerResource->checkCustomerId($customerId);
        }
        return $this->_isCustomerIdChecked;
    }

    /**
     * Customer authorization
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
    public function login($username, $password)
    {
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $this->_createCustomer()->setWebsiteId($this->_storeManager->getStore()->getWebsiteId());

        if ($customer->authenticate($username, $password)) {
            $this->setCustomerAsLoggedIn($customer);
            $this->_renewSession();
            return true;
        }
        return false;
    }

    public function setCustomerAsLoggedIn($customer)
    {
        $this->setCustomer($customer);
        $this->_eventManager->dispatch('customer_login', array('customer'=>$customer));
        return $this;
    }

    /**
     * Authorization customer by identifier
     *
     * @param   int $customerId
     * @return  bool
     */
    public function loginById($customerId)
    {
        $customer = $this->_createCustomer()->load($customerId);
        if ($customer->getId()) {
            $this->setCustomerAsLoggedIn($customer);
            $this->_renewSession();
            return true;
        }
        return false;
    }

    /**
     * Logout customer
     *
     * @return \Magento\Customer\Model\Session
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            $this->_eventManager->dispatch('customer_logout', array('customer' => $this->getCustomer()) );
            $this->_logout();
        }
        return $this;
    }

    /**
     * Authenticate controller action by login customer
     *
     * @param   \Magento\App\Action\Action $action
     * @param   bool $loginUrl
     * @return  bool
     */
    public function authenticate(\Magento\App\Action\Action $action, $loginUrl = null)
    {
        if ($this->isLoggedIn()) {
            return true;
        }
        $this->setBeforeAuthUrl($this->_createUrl()->getUrl('*/*/*', array('_current' => true)));
        if (isset($loginUrl)) {
            $action->getResponse()->setRedirect($loginUrl);
        } else {
            $arguments = $this->_customerData->getLoginUrlParams();
            if ($this->_session->getCookieShouldBeReceived() && $this->_url->getUseSession()) {
                $arguments += array('_query' => array(
                    $this->_session->getSessionIdQueryParam() => $this->_session->getSessionId()
                ));
            }
            $action->getResponse()->setRedirect(
                $this->_url->getUrl(\Magento\Customer\Helper\Data::ROUTE_ACCOUNT_LOGIN, $arguments)
            );
        }

        return false;
    }

    /**
     * Set auth url
     *
     * @param string $key
     * @param string $url
     * @return \Magento\Customer\Model\Session
     */
    protected function _setAuthUrl($key, $url)
    {
        $url = $this->_coreUrl->removeRequestParam($url, $this->_session->getSessionIdQueryParam());
        // Add correct session ID to URL if needed
        $url = $this->_createUrl()->getRebuiltUrl($url);
        return $this->setData($key, $url);
    }

    /**
     * Logout without dispatching event
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _logout()
    {
        $this->setId(null);
        $this->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID);
        $this->getCookie()->delete($this->getSessionName());
        return $this;
    }

    /**
     * Set Before auth url
     *
     * @param string $url
     * @return \Magento\Customer\Model\Session
     */
    public function setBeforeAuthUrl($url)
    {
        return $this->_setAuthUrl('before_auth_url', $url);
    }

    /**
     * Set After auth url
     *
     * @param string $url
     * @return \Magento\Customer\Model\Session
     */
    public function setAfterAuthUrl($url)
    {
        return $this->_setAuthUrl('after_auth_url', $url);
    }

    /**
     * Reset core session hosts after reseting session ID
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _renewSession()
    {
        parent::_renewSession();
        $this->_cleanHosts();
        return $this;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function _createCustomer()
    {
        return $this->_customerFactory->create();
    }

    /**
     * @return \Magento\Core\Model\Url
     */
    protected function _createUrl()
    {
        return $this->_urlFactory->create();
    }
}
