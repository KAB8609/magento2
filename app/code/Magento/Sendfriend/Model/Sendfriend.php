<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * SendFriend Log
 *
 * @method \Magento\Sendfriend\Model\Resource\Sendfriend _getResource()
 * @method \Magento\Sendfriend\Model\Resource\Sendfriend getResource()
 * @method int getIp()
 * @method \Magento\Sendfriend\Model\Sendfriend setIp(int $value)
 * @method int getTime()
 * @method \Magento\Sendfriend\Model\Sendfriend setTime(int $value)
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sendfriend\Model;

class Sendfriend extends \Magento\Core\Model\AbstractModel
{
    /**
     * Recipient Names
     *
     * @var array
     */
    protected $_names   = array();

    /**
     * Recipient Emails
     *
     * @var array
     */
    protected $_emails  = array();

    /**
     * Sender data array
     *
     * @var array
     */
    protected $_sender  = array();

    /**
     * Product Instance
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * Count of sent in last period
     *
     * @var int
     */
    protected $_sentCount;

    /**
     * Last values for Cookie
     *
     * @var string
     */
    protected $_lastCookieValue = array();

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sendfriend\Model\Resource\Sendfriend');
    }

    /**
     * Retrieve Data Helper
     *
     * @return \Magento\Sendfriend\Helper\Data
     */
    protected function _getHelper()
    {
        return \Mage::helper('Magento\Sendfriend\Helper\Data');
    }

    public function send()
    {
        if ($this->isExceedLimit()){
            \Mage::throwException(__('You\'ve met your limit of %1 sends in an hour.', $this->getMaxSendsToFriend()));
        }

        /* @var $translate \Magento\Core\Model\Translate */
        $translate = \Mage::getSingleton('Magento\Core\Model\Translate');
        $translate->setTranslateInline(false);

        /* @var $mailTemplate \Magento\Core\Model\Email\Template */
        $mailTemplate = \Mage::getModel('Magento\Core\Model\Email\Template');

        $message = nl2br(htmlspecialchars($this->getSender()->getMessage()));
        $sender  = array(
            'name'  => $this->_getHelper()->escapeHtml($this->getSender()->getName()),
            'email' => $this->_getHelper()->escapeHtml($this->getSender()->getEmail())
        );

        $mailTemplate->setDesignConfig(array(
            'area'  => \Magento\Core\Model\App\Area::AREA_FRONTEND,
            'store' => \Mage::app()->getStore()->getId()
        ));

        foreach ($this->getRecipients()->getEmails() as $k => $email) {
            $name = $this->getRecipients()->getNames($k);
            $mailTemplate->sendTransactional(
                $this->getTemplate(),
                $sender,
                $email,
                $name,
                array(
                    'name'          => $name,
                    'email'         => $email,
                    'product_name'  => $this->getProduct()->getName(),
                    'product_url'   => $this->getProduct()->getUrlInStore(),
                    'message'       => $message,
                    'sender_name'   => $sender['name'],
                    'sender_email'  => $sender['email'],
                    'product_image' => \Mage::helper('Magento\Catalog\Helper\Image')->init($this->getProduct(),
                        'small_image')->resize(75),
                )
            );
        }

        $translate->setTranslateInline(true);
        $this->_incrementSentCount();

        return $this;
    }

    /**
     * Validate Form data
     *
     * @return bool|array
     */
    public function validate()
    {
        $errors = array();

        $name = $this->getSender()->getName();
        if (empty($name)) {
            $errors[] = __('The sender name cannot be empty.');
        }

        $email = $this->getSender()->getEmail();
        if (empty($email) OR !Zend_Validate::is($email, 'EmailAddress')) {
            $errors[] = __('Invalid Sender Email');
        }

        $message = $this->getSender()->getMessage();
        if (empty($message)) {
            $errors[] = __('The message cannot be empty.');
        }

        if (!$this->getRecipients()->getEmails()) {
            $errors[] = __('At least one recipient must be specified.');
        }

        // validate recipients email addresses
        foreach ($this->getRecipients()->getEmails() as $email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $errors[] = __('Please enter a correct recipient email address.');
                break;
            }
        }

        $maxRecipients = $this->getMaxRecipients();
        if (count($this->getRecipients()->getEmails()) > $maxRecipients) {
            $errors[] = __('No more than %1 emails can be sent at a time.', $this->getMaxRecipients());
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    /**
     * Set cookie instance
     *
     * @param \Magento\Core\Model\Cookie $product
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    public function setCookie($cookie)
    {
        return $this->setData('_cookie', $cookie);
    }

    /**
     * Retrieve Cookie instance
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Model\Cookie
     */
    public function getCookie()
    {
        $cookie = $this->_getData('_cookie');
        if (!$cookie instanceof \Magento\Core\Model\Cookie) {
            \Mage::throwException(__('Please define a correct Cookie instance.'));
        }
        return $cookie;
    }

    /**
     * Set Visitor Remote Address
     *
     * @param int $ipAddr the IP address on Long Format
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    public function setRemoteAddr($ipAddr)
    {
        $this->setData('_remote_addr', $ipAddr);
        return $this;
    }

    /**
     * Retrieve Visitor Remote Address
     *
     * @return int
     */
    public function getRemoteAddr()
    {
        return $this->_getData('_remote_addr');
    }

    /**
     * Set Website Id
     *
     * @param int $id - website id
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    public function setWebsiteId($id)
    {
        $this->setData('_website_id', $id);
        return $this;
    }

    /**
     * Retrieve Website Id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->_getData('_website_id');
    }

    /**
     * Set Recipients
     *
     * @param array $recipients
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    public function setRecipients($recipients)
    {
        // validate array
        if (!is_array($recipients) OR !isset($recipients['email'])
            OR !isset($recipients['name']) OR !is_array($recipients['email'])
            OR !is_array($recipients['name'])) {
            return $this;
        }

        $emails = array();
        $names  = array();
        foreach ($recipients['email'] as $k => $email) {
            if (!isset($emails[$email]) && isset($recipients['name'][$k])) {
                $emails[$email] = true;
                $names[] = $recipients['name'][$k];
            }
        }

        if ($emails) {
            $emails = array_keys($emails);
        }

        return $this->setData('_recipients', new \Magento\Object(array(
            'emails' => $emails,
            'names'  => $names
        )));
    }

    /**
     * Retrieve Recipients object
     *
     * @return \Magento\Object
     */
    public function getRecipients()
    {
        $recipients = $this->_getData('_recipients');
        if (!$recipients instanceof \Magento\Object) {
            $recipients =  new \Magento\Object(array(
                'emails' => array(),
                'names'  => array()
            ));
            $this->setData('_recipients', $recipients);
        }
        return $recipients;
    }

    /**
     * Set product instance
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    public function setProduct($product)
    {
        return $this->setData('_product', $product);
    }

    /**
     * Retrieve Product instance
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        $product = $this->_getData('_product');
        if (!$product instanceof \Magento\Catalog\Model\Product) {
            \Mage::throwException(__('Please define a correct Product instance.'));
        }
        return $product;
    }

    /**
     * Set Sender Information array
     *
     * @param array $sender
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    public function setSender($sender)
    {
        if (!is_array($sender)) {
            __('Invalid Sender Information');
        }

        return $this->setData('_sender', new \Magento\Object($sender));
    }

    /**
     * Retrieve Sender Information Object
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Object
     */
    public function getSender()
    {
        $sender = $this->_getData('_sender');
        if (!$sender instanceof \Magento\Object) {
            \Mage::throwException(__('Please define the correct Sender information.'));
        }
        return $sender;
    }

    /**
     * Get max allowed uses of "Send to Friend" function per hour
     *
     * @return integer
     */
    public function getMaxSendsToFriend()
    {
        return $this->_getHelper()->getMaxEmailPerPeriod();
    }

    /**
     * Get current Email "Send to friend" template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->_getHelper()->getEmailTemplate();
    }

    /**
     * Get max allowed recipients for "Send to a Friend" function
     *
     * @return integer
     */
    public function getMaxRecipients()
    {
        return $this->_getHelper()->getMaxRecipients();
    }

    /**
     * Check if user is allowed to email product to a friend
     *
     * @return boolean
     */
    public function canEmailToFriend()
    {
        return $this->_getHelper()->isEnabled();
    }

    /**
     * Check if user is exceed limit
     *
     * @return boolean
     */
    public function isExceedLimit()
    {
        return $this->getSentCount() >= $this->getMaxSendsToFriend();
    }

    /**
     * Return count of sent in last period
     *
     * @param bool $useCache - flag, is allow to use value of attribute of model if it is processed last time
     * @return int
     */
    public function getSentCount($useCache = true)
    {
        if ($useCache && !is_null($this->_sentCount)) {
            return $this->_sentCount;
        }

        switch ($this->_getHelper()->getLimitBy()) {
            case \Magento\Sendfriend\Helper\Data::CHECK_COOKIE:
                return $this->_sentCount = $this->_sentCountByCookies(false);
            case \Magento\Sendfriend\Helper\Data::CHECK_IP:
                return $this->_sentCount = $this->_sentCountByIp(false);
            default:
                return 0;
        }
    }

    /**
     * Increase count of sent
     *
     * @return int
     */
    protected function _incrementSentCount()
    {
        switch ($this->_getHelper()->getLimitBy()) {
            case \Magento\Sendfriend\Helper\Data::CHECK_COOKIE:
                return $this->_sentCount = $this->_sentCountByCookies(true);
            case \Magento\Sendfriend\Helper\Data::CHECK_IP:
                return $this->_sentCount = $this->_sentCountByIp(true);
            default:
                return 0;
        }
    }

    /**
     * Return count of sent in last period by cookie
     *
     * @param bool $increment - flag, increase count before return value
     * @return int
     */
    protected function _sentCountByCookies($increment = false)
    {
        $cookie   = $this->_getHelper()->getCookieName();
        $time     = time();
        $newTimes = array();

        if (isset($this->_lastCookieValue[$cookie])) {
            $oldTimes = $this->_lastCookieValue[$cookie];
        } else {
            $oldTimes = $this->getCookie()->get($cookie);
        }

        if ($oldTimes) {
            $oldTimes = explode(',', $oldTimes);
            foreach ($oldTimes as $oldTime) {
                $periodTime = $time - $this->_getHelper()->getPeriod();
                if (is_numeric($oldTime) AND $oldTime >= $periodTime) {
                    $newTimes[] = $oldTime;
                }
            }
        }

        if ($increment) {
            $newTimes[] = $time;
            $newValue = implode(',', $newTimes);
            $this->getCookie()->set($cookie, $newValue);
            $this->_lastCookieValue[$cookie] = $newValue;
        }

        return count($newTimes);
    }
    /**
     * Return count of sent in last period by IP address
     *
     * @param bool $increment - flag, increase count before return value
     * @return int
     */
    protected function _sentCountByIp($increment = false)
    {
        $time   = time();
        $period = $this->_getHelper()->getPeriod();
        $websiteId = $this->getWebsiteId();

        if ($increment) {
            // delete expired logs
            $this->_getResource()->deleteLogsBefore($time - $period);
            // add new item
            $this->_getResource()->addSendItem($this->getRemoteAddr(), $time, $websiteId);
        }

        return $this->_getResource()->getSendCount($this, $this->getRemoteAddr(), time() - $period, $websiteId);
    }
    /**
     * Register self in global register with name send_to_friend_model
     *
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    public function register()
    {
        if (!\Mage::registry('send_to_friend_model')) {
            \Mage::register('send_to_friend_model', $this);
        }
        return $this;
    }
}
