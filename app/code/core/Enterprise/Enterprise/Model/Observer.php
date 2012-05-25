<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise general observer
 *
 */
class Enterprise_Enterprise_Model_Observer
{
    /**
     * Set hide survey question to session
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Enterprise_Model_Observer
     */
    public function setHideSurveyQuestion($observer)
    {
        Mage::getSingleton('Mage_Backend_Model_Auth_Session')->setHideSurveyQuestion(true);
        return $this;
    }
}
