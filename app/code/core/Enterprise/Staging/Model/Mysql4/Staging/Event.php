<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Staging_Model_Mysql4_Staging_Event extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_event', 'event_id');
    }

    /**
     * Prepare some data before save processing
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  Enterprise_Staging_Model_Mysql4_Staging_Event
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $staging = $object->getStaging();
        if ($staging instanceof Enterprise_Staging_Model_Staging) {
            if ($staging->getId()) {
                $object->setStagingId($staging->getId());
                $object->setStagingWebsiteId($staging->getStagingWebsiteId());
                $object->setMasterWebsiteId($staging->getMasterWebsiteId());
            }
        }

        if (!$object->getId()) {
            $object->setIsNew(true);
            $value = Mage::getModel('core/date')->gmtDate();
            $object->setCreatedAt($value);
        } else {
            $value = Mage::getModel('core/date')->gmtDate();
            $object->setUpdatedAt($value);
        }

        $ip = Mage::app()->getRequest()->getServer('REMOTE_ADDR');
        $object->setIp(ip2long($ip));

        $user = Mage::getSingleton('admin/session')->getUser();
        if ($user) {
            $object->setUserId($user->getId());
            $object->setUsername($user->getName());
        }

        return parent::_beforeSave($object);
    }
}
