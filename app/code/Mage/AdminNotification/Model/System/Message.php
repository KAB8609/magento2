<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_AdminNotification_Model_System_Message extends Mage_Core_Model_Abstract
    implements Mage_AdminNotification_Model_System_MessageInterface
{
    protected function _construct()
    {
        $this->_init('Mage_AdminNotification_Model_Resource_System_Message');
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return true;
    }

    /**
     * Retrieve message text
     *
     * @return text
     */
    public function getText()
    {
        return $this->getData('text');
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return $this->_getData('severity');
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->_getData('identity');
    }
}
