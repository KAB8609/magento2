<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store and language switcher block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Page_Block_Switch extends Mage_Core_Block_Template
{
    public function getCurrentWebsiteId()
    {
        return Mage::app()->getStore()->getWebsiteId();
    }

    public function getCurrentGroupId()
    {
        return Mage::app()->getStore()->getGroupId();
    }

    public function getCurrentStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function getRawGroups()
    {
        if (!$this->hasData('raw_groups')) {
            $collection = Mage::getModel('core/store_group')
                ->getCollection()
                ->addWebsiteFilter($this->getCurrentWebsiteId())
                ->load();
            $groups = array();
            foreach ($collection as $group) {
                $groups[$group->getId()] = $group;
            }
            $this->setData('raw_groups', $groups);
        }
        return $this->getData('raw_groups');
    }

    public function getRawStores()
    {
        if (!$this->hasData('raw_stores')) {
            $collection = Mage::getModel('core/store')
                ->getCollection()
                ->addWebsiteFilter($this->getCurrentWebsiteId())
                ->addFilter('is_active', 1)
                ->load();
            $stores = array();
            foreach ($collection as $store) {
                $store->setLocaleCode(Mage::getStoreConfig('general/locale/code', $store->getId()));
                $store->setHomeUrl($store->getBaseUrl().'?store='.$store->getCode());
                $stores[$store->getGroupId()][$store->getId()] = $store;
            }
            $this->setData('raw_stores', $stores);
        }
        return $this->getData('raw_stores');
    }

    public function getStores()
    {
        if (!$this->hasData('stores')) {
            $rawGroups = $this->getRawGroups();
            $rawStores = $this->getRawStores();

            $stores = array();
            $localeCode = Mage::getStoreConfig('general/locale/code');
            foreach ($rawGroups as $group) {
                if (!isset($rawStores[$group->getId()])) {
                    continue;
                }
                if ($group->getId() == Mage::app()->getStore()->getGroupId()) {
                    $stores[] = Mage::app()->getStore();
                    continue;
                }
                $useStore = false;
                foreach ($rawStores[$group->getId()] as $store) {
                    if ($store->getLocaleCode() == $localeCode) {
                        $useStore = true;
                        $stores[] = $store;
                    }
                }
                if (!$useStore && isset($rawStores[$group->getId()][$group->getDefaultStoreId()])) {
                    $stores[] = $rawStores[$group->getId()][$group->getDefaultStoreId()];
                }
            }
            $this->setData('stores', $stores);
        }
        return $this->getData('stores');
    }

    public function getLanguages()
    {
        if (!$this->getData('languages')) {
            $rawStores = $this->getRawStores();

            $groupId = $this->getCurrentGroupId();
            if (!isset($rawStores[$groupId])) {
                $languages = array();
            } else {
                $languages = $rawStores[$groupId];
            }
            $this->setData('languages', $languages);
        }
        return $this->getData('languages');
    }
}
