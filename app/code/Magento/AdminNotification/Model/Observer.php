<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * AdminNotification observer
 *
 * @category   Mage
 * @package    Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdminNotification_Model_Observer
{
    /**
     * Predispath admin action controller
     *
     * @param Magento_Event_Observer $observer
     */
    public function preDispatch(Magento_Event_Observer $observer)
    {

        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isLoggedIn()) {

            $feedModel  = Mage::getModel('Magento_AdminNotification_Model_Feed');
            /* @var $feedModel Magento_AdminNotification_Model_Feed */

            $feedModel->checkUpdate();
        }
    }
}
