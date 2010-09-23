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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Create filter object by key
     *
     * @param string $key
     * @return Mage_Catalog_Model_Layer_Filter_Abstract
     */
    public function getFilterByKey($key)
    {
        $filterModelName = 'catalog/layer_filter_attribute';
        switch ($key) {
            case 'price':
                $filterModelName = 'catalog/layer_filter_price';
                break;
            case 'decimal':
                $filterModelName = 'catalog/layer_filter_decimal';
                break;
            case 'category':
                $filterModelName = 'catalog/layer_filter_category';
                break;
            default:
                $filterModelName = 'catalog/layer_filter_attribute';
                break;
        }
        return Mage::getModel($filterModelName);
    }

    /**
     * Export $this->_getUrl() function to public
     *
     * @param string $route
     * @param array $params
     * @return array
     */
    public function getUrl($route, $params = array())
    {
        return $this->_getUrl($route, $params);
    }


    /**
     * Retrieve country options array
     *
     * @return array
     */
    public function getCountryOptionsArray()
    {
        Varien_Profiler::start('TEST: '.__METHOD__);

        $cacheKey = 'XMLCONNECT_COUNTRY_SELECT_STORE_'.Mage::app()->getStore()->getCode();
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $options = Mage::getModel('directory/country')
                ->getResourceCollection()
                ->loadByStore()
                ->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        Varien_Profiler::stop('TEST: '.__METHOD__);
        return $options;
    }

    /**
     * Get list of predefined and supported Devices
     *
     * @return array
     */
    static public function getSupportedDevices()
    {
        $devices = array (
            'iphone' => Mage::helper('xmlconnect')->__('iPhone')
        );

        return $devices;
    }

    /**
     * Get list of predefined and supported Devices
     *
     * @return array
     */
    public function getStatusOptions()
    {
        $options = array (
            Mage_XmlConnect_Model_Application::APP_STATUS_SUCCESS => Mage::helper('xmlconnect')->__('Submitted'),
            Mage_XmlConnect_Model_Application::APP_STATUS_INACTIVE => Mage::helper('xmlconnect')->__('Not Submitted'),
        );
        return $options;
    }

    /**
     * Retrieve supported device types as "html select options"
     *
     * @return array
     */
    public function getDeviceTypeOptions()
    {
        $devices = self::getSupportedDevices();
        $options = array();
        if (count($devices) > 1) {
            $options[] = array('value' => '', 'label' => Mage::helper('xmlconnect')->__('Please Select Device Type'));
        }
        foreach ($devices as $type => $label) {
            $options[] = array('value' => $type, 'label' => $label);
        }
        return $options;
    }

    /**
     * Get default application tabs
     *
     * @return array
     */
    public function getDefaultApplicationDesignTabs()
    {
        $tabs = array(
            array(
                'label' => Mage::helper('xmlconnect')->__('Home'),
                'image' => 'tab_home.png',
                'action' => 'Home',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('Shop'),
                'image' => 'tab_shop.png',
                'action' => 'Shop',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('Search'),
                'image' => 'tab_search.png',
                'action' => 'Search',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('Cart'),
                'image' => 'tab_cart.png',
                'action' => 'Cart',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('More'),
                'image' => 'tab_more.png',
                'action' => 'More',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('Account'),
                'image' => 'tab_account.png',
                'action' => 'Account',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('More Info'),
                'image' => 'tab_page.png',
                'action' => 'AboutUs',
            ),
        );

        return $tabs;
    }

    /**
     * Retrieve xmlconnect images skin url
     *
     * @param string $name
     * @return string
     */
    public function getSkinImagesUrl($name = null)
    {
        return Mage::getDesign()->getSkinUrl('images/xmlconnect/' . $name);
    }

    /**
     * Merges $changes array to $target array recursive, overwriting existing key,  and adding new one
     * @static
     * @param  $target
     * @param  $changes
     * @return array
     */
    static public function arrayMergeRecursive($target, $changes)
    {
        if(!is_array($target)) {
            $target = empty($target) ? array() : array($target);
        }
        if(!is_array($changes)) {
            $changes = array($changes);
        }
        foreach($changes as $key => $value) {
            if(!array_key_exists($key, $target) and !is_numeric($key)) {
                $target[$key] = $changes[$key];
                continue;
            }
            if(is_array($value) or is_array($target[$key])) {
                $target[$key] = self::arrayMergeRecursive($target[$key], $changes[$key]);
            } else if(is_numeric($key)) {
                if(!in_array($value, $target)) {
                    $target[] = $value;
                }
            } else {
                $target[$key] = $value;
            }
        }

        return $target;
    }

    /**
     * Wrap $body with HTML4 headers
     *
     * @param string $text
     * @return string
     */
    public function htmlize($body)
    {
        return <<<EOT
&lt;!DOCTYPE html PUBLIC &quot;-//W3C//DTD XHTML 1.0 Strict//EN&quot; &quot;http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd&quot;&gt;
&lt;html xmlns=&quot;http://www.w3.org/1999/xhtml&quot; xml:lang=&quot;en&quot; lang=&quot;en&quot;&gt;
&lt;head&gt;
&lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;style.css&quot; media=&quot;screen&quot;/&gt;
&lt;/head&gt;
&lt;body&gt;$body&lt;/body&gt;&lt;/html&gt;
EOT;
    }
}
