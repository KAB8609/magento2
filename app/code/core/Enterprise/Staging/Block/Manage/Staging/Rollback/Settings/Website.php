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


/**
 * Staging rollback settings of staging website type block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Rollback_Settings_Website extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/staging/manage/staging/rollback/settings/website.phtml');
        $this->setId('staging_website_mapper');
        $this->setUseAjax(true);

        $this->setRollbackJsObjectName('enterpriseRollbackForm');
        $this->setRowInitCallback($this->getRollbackJsObjectName().'.stagingWebsiteMapperRowInit');
        $this->setIsReadyForRollback(true);
    }

    /**
     * Prepare layout
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareLayout()
    {
        $this->setChild('website_store_form',
            $this->getLayout()
                ->createBlock('enterprise_staging/manage_staging_rollback_settings_store')
        );

        $this->setChild('rollbackGrid',
            $this->getLayout()->createBlock('enterprise_staging/manage_staging_rollback_grid', 'staging.rollback.grid')
                ->setExtendInfo($this->getBackup()->getItemVersionCheck()));

        return parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited backup object
     *
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    public function getBackup()
    {
        if (!($this->getData('staging_backup') instanceof Enterprise_Staging_Model_Staging_Backup)) {
            $this->setData('staging_backup', Mage::registry('staging_backup'));
        }
        return $this->getData('staging_backup');
    }

    /**
     * Retrieve event
     *
     * @return Enterprise_Staging_Block_Manage_Staging-Event
     */
    public function getEvent()
    {
        if (!($this->getData('staging_event') instanceof Enterprise_Staging_Model_Staging_Event)) {
            $this->setData('staging_event', $this->getBackup()->getEvent());
        }
        return $this->getData('staging_event');
    }

    /**
     * Retrieve staging object of current event
     *
     * @return Enterprise_Staging_Block_Manage_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', $this->getBackup()->getStaging());
        }
        return $this->getData('staging');
    }

    /**
     * Return mapper instance
     */
    public function getMapper()
    {
        return $this->getStaging()->getMapperInstance();
    }

    /**
     * Return Save url
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/rollbackPost', array('_current'=>true));
    }

    /**
     * Return website collection
     *
     * @return object
     */
    public function getWebsiteCollection()
    {
        return Mage::app()->getWebsites();
    }

    /**
     * return store collection
     *
     * @return object
     */
    public function getAllStoresCollection()
    {
        return Mage::app()->getStores();
    }

    /**
     * return stores as json
     *
     * @return string
     */
    public function getAllStoresJson()
    {
        $stores = array();
        foreach ($this->getAllStoresCollection() as $store) {
            $stores[$store->getWebsiteId()][] = $store->getData();
        }
        if (!$stores) {
            return '{}';
        } else {
            return Zend_Json::encode($stores);
        }
    }

    /**
     * return main buttons as html structure
     *
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = '';
        //$html = parent::getMainButtonsHtml();
        if($this->getIsReadyForRollback()){
            $html.= $this->getChildHtml('rollback_button');
        }
        return $html;
    }
}