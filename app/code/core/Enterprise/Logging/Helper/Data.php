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
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Logging_Helper_Data extends Mage_Core_Helper_Abstract 
{
    /**
     * configuration
     */
    const   CONFIG_FILE = 'logging.xml';
    private $_config;
    private $_labels;

    public function loadConfig()
    {
        if ( !($conf = Mage::app()->loadCache('actions_to_log')) ) {
            $conf = $this->_getActionsConfigFromXml();
            $result = Mage::app()->saveCache(serialize($conf), 'actions_to_log');
        } else {
            $conf = unserialize($conf);
        }
        $this->_config = $conf;

        if ( !($conf = Mage::app()->loadCache('actions_to_log_labels')) ) {
            $conf = $this->_getLabelsConfigFromXml(); 
            Mage::app()->saveCache(serialize($conf['list']), 'actions_to_log_labels');
            $conf = $conf['list'];
        } else {
            $conf = unserialize($conf);
        }
        $this->_labels = $conf;
    }

    /**
     * Filter if we need to log this action
     *
     * @param string action - fullActionName with removed 'adminhtml_' prefix
     */
    public function isActive($action)
    {
        if (!isset($this->_config)) {
            $this->loadConfig();
        }
        $current = isset($this->_config[$action]) ? $this->_config[$action] : false;
        if (!$current) {
            return false;
        }

        $code = $current['event'];
        /**
         * Note that /default/logging/enabled/products - is an indicator if the products should be logged
         * but /enterprise/logging/event/products - is a node where event info stored.
         */
        $node = Mage::getConfig()->getNode('default/admin/enterprise_logging/' . $code);
        return ( (string)$node == '1' ? true : false);
    }

    /**
     * Return, previously stored in cache config
     */ 
    public function getConfig($action) 
    {
        if (!isset($this->_config)) {
            $this->loadConfig();
        }
        if (!isset($this->_config[$action])) {
            return null;
        }
        $this->_config[$action]['base_action'] = $action;
        return $this->_config[$action];
    }

    /**
     * Get all labels
     */
    public function getLabels() 
    {
        if (!isset($this->_labels)) {
            $this->loadConfig();
        }
        return $this->_labels;
    }

    /**
     * Get label for current event_code
     */
    public function getLabel($code) 
    {
        if (!isset($this->_labels)) {
            $this->loadConfig();
        }
        $labelsconfig = $this->getLabels();
        return isset($labelsconfig[$code]) ? $labelsconfig[$code] : "";
    }


    /**
     * Load actions from config
     */
    private function _getActionsConfigFromXml() 
    {
        $config = Mage::getConfig();
        $modules = $config->getNode('modules')->children();

        // check if local modules are disabled
        $disableLocalModules = (string)$config->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));
        $conf = Mage::getModel('core/config');
        $is_loaded = false;
        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                if ($disableLocalModules && ('local' === (string)$module->codePool)) {
                    continue;
                }

                $configFile = $config->getModuleDir('etc', $modName) . DS . self::CONFIG_FILE;
                $logConfig = Mage::getModel('core/config_base');
                if ($logConfig->loadFile($configFile) ) {
                    if (!$is_loaded) {
                        $conf->loadFile($configFile);
                        $is_loaded = true;
                    } else
                        $conf->extend($logConfig, true);
                } 
            }
        }
        return $conf->getNode('actions')->asArray();
    }

    /**
     * Load labels from configuration file
     */
    private function _getLabelsConfigFromXml() 
    {
        $config = Mage::getConfig();
        $modules = $config->getNode('modules')->children();

        // check if local modules are disabled
        $disableLocalModules = (string)$config->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));
        $conf = Mage::getModel('core/config');
        $is_loaded = false;
        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                if ($disableLocalModules && ('local' === (string)$module->codePool)) {
                    continue;
                }

                $configFile = $config->getModuleDir('etc', $modName) . DS . self::CONFIG_FILE;
                $logConfig = Mage::getModel('core/config_base');
                if ($logConfig->loadFile($configFile) ) {
                    if (!$is_loaded) {
                        $conf->loadFile($configFile);
                        $is_loaded = true;
                    } else
                        $conf->extend($logConfig, true);
                } 
            }
        }
        return $conf->getNode('labels')->asArray();
    }
}