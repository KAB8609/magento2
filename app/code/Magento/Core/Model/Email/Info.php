<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Email information model
 * Email message may contain addresses in any of these three fields:
 *  -To:  Primary recipients
 *  -Cc:  Carbon copy to secondary recipients and other interested parties
 *  -Bcc: Blind carbon copy to tertiary recipients who receive the message
 *        without anyone else (including the To, Cc, and Bcc recipients) seeing who the tertiary recipients are
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Email_Info extends \Magento\Object
{
    /**
     * Name list of "Bcc" recipients
     *
     * @var array
     */
    protected $_bccNames = array();

    /**
     * Email list of "Bcc" recipients
     *
     * @var array
     */
    protected $_bccEmails = array();

    /**
     * Name list of "To" recipients
     *
     * @var array
     */
    protected $_toNames = array();

    /**
     * Email list of "To" recipients
     *
     * @var array
     */
    protected $_toEmails = array();


    /**
     * Add new "Bcc" recipient to current email
     *
     * @param string $email
     * @param string|null $name
     * @return Magento_Core_Model_Email_Info
     */
    public function addBcc($email, $name = null)
    {
        array_push($this->_bccNames, $name);
        array_push($this->_bccEmails, $email);
        return $this;
    }

    /**
     * Add new "To" recipient to current email
     *
     * @param string $email
     * @param string|null $name
     * @return Magento_Core_Model_Email_Info
     */
    public function addTo($email, $name = null)
    {
        array_push($this->_toNames, $name);
        array_push($this->_toEmails, $email);
        return $this;
    }

    /**
     * Get the name list of "Bcc" recipients
     *
     * @return array
     */
    public function getBccNames()
    {
        return $this->_bccNames;
    }

    /**
     * Get the email list of "Bcc" recipients
     *
     * @return array
     */
    public function getBccEmails()
    {
        return $this->_bccEmails;
    }

    /**
     * Get the name list of "To" recipients
     *
     * @return array
     */
    public function getToNames()
    {
        return $this->_toNames;
    }

    /**
     * Get the email list of "To" recipients
     *
     * @return array
     */
    public function getToEmails()
    {
        return $this->_toEmails;
    }
}
