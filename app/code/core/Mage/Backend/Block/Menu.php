<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend menu block
 *
 * @method Mage_Backend_Block_Menu setAdditionalCacheKeyInfo(array $cacheKeyInfo)
 * @method array getAdditionalCacheKeyInfo()
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Menu extends Mage_Backend_Block_Template
{
    const CACHE_TAGS = 'BACKEND_MAINMENU';

    /**
     * Backend URL instance
     *
     * @var Mage_Backend_Model_Url
     */
    protected $_url;

    /**
     * Initialize template and cache settings
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Mage_Backend::menu.phtml');
        $this->_url = Mage::getModel('Mage_Backend_Model_Url');
        $this->setCacheTags(array(self::CACHE_TAGS));
    }

    /**
     * Retrieve cache lifetime
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return 86400;
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = array(
            'admin_top_nav',
            $this->getActive(),
            Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getId(),
            Mage::app()->getLocale()->getLocaleCode()
        );
        // Add additional key parameters if needed
        $additionalCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
        if (is_array($additionalCacheKeyInfo) && !empty($additionalCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $additionalCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }

    /**
     * Retrieve Adminhtml Menu array
     *
     * @return array
     */
    public function getMenuArray()
    {
        return $this->_buildMenuArray();
    }

    /**
     * Retrieve Title value for menu node
     *
     * @param Varien_Simplexml_Element $child
     * @return string
     */
    protected function _getHelperValue(Varien_Simplexml_Element $child)
    {
        $helperName         = 'Mage_Backend_Helper_Data';
        $titleNodeName      = 'title';
        $childAttributes    = $child->attributes();
        if (isset($childAttributes['module'])) {
            $helperName     = (string)$childAttributes['module'];
        }
//        if (isset($childAttributes['translate'])) {
//            $titleNodeName  = (string)$childAttributes['translate'];
//        }

        return Mage::helper($helperName)->__((string)$child->$titleNodeName);
    }

    /**
     * Recursive Build Menu array
     *
     * @param Varien_Simplexml_Element $parent
     * @param string $path
     * @param int $level
     * @return array
     */
    protected function _buildMenuArray(Varien_Simplexml_Element $parent=null, $path='', $level=0)
    {
        if (is_null($parent)) {
            $parent = Mage::getSingleton('Mage_Admin_Model_Config')->getAdminhtmlConfig()->getNode('menu');
        }

        $parentArr = array();
        $sortOrder = 0;
        foreach ($parent->children() as $childName => $child) {
            if (1 == $child->disabled) {
                continue;
            }

            $aclResource = 'admin/' . ($child->resource ? (string)$child->resource : $path . $childName);
            if (!$this->_checkAcl($aclResource) || !$this->_isEnabledModuleOutput($child)) {
                continue;
            }

            if ($child->depends && !$this->_checkDepends($child->depends)) {
                continue;
            }

            $menuArr = array();

            $menuArr['label'] = $this->_getHelperValue($child);

            $menuArr['sort_order'] = $child->sort_order ? (int)$child->sort_order : $sortOrder;

            if ($child->action) {
                $menuArr['url'] = $this->_url->getUrl((string)$child->action, array('_cache_secret_key' => true));
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }

            $menuArr['active'] = ($this->getActive()==$path.$childName)
                || (strpos($this->getActive(), $path.$childName.'/')===0);

            $menuArr['level'] = $level;

            if ($child->children) {
                $menuArr['children'] = $this->_buildMenuArray($child->children, $path.$childName.'/', $level+1);
            }
            $parentArr[$childName] = $menuArr;

            $sortOrder++;
        }

        uasort($parentArr, array($this, '_sortMenu'));

        while (list($key, $value) = each($parentArr)) {
            $last = $key;
        }
        if (isset($last)) {
            $parentArr[$last]['last'] = true;
        }

        return $parentArr;
    }

    /**
     * Sort menu comparison function
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    protected function _sortMenu($a, $b)
    {
        return $a['sort_order']<$b['sort_order'] ? -1 : ($a['sort_order']>$b['sort_order'] ? 1 : 0);
    }

    /**
     * Check Depends
     *
     * @param Varien_Simplexml_Element $depends
     * @return bool
     */
    protected function _checkDepends(Varien_Simplexml_Element $depends)
    {
        if ($depends->module) {
            $modulesConfig = Mage::getConfig()->getNode('modules');
            foreach ($depends->module as $module) {
                if (!$modulesConfig->$module || !$modulesConfig->$module->is('active')) {
                    return false;
                }
            }
        }

        if ($depends->config) {
            foreach ($depends->config as $path) {
                if (!Mage::getStoreConfigFlag((string)$path)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check is Allow menu item for admin user
     *
     * @param string $resource
     * @return bool
     */
    protected function _checkAcl($resource)
    {
        try {
            $res =  Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed($resource);
        } catch (Exception $e) {
            return false;
        }
        return $res;
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $html = preg_replace_callback('#'.Mage_Backend_Model_Url::SECRET_KEY_PARAM_NAME.'/\$([^\/].*)/([^\$].*)\$#U', array($this, '_callbackSecretKey'), $html);

        return $html;
    }

    /**
     * Replace Callback Secret Key
     *
     * @param array $match
     * @return string
     */
    protected function _callbackSecretKey($match)
    {
        return Mage_Backend_Model_Url::SECRET_KEY_PARAM_NAME . '/'
            . $this->_url->getSecretKey($match[1], $match[2]);
    }

    /**
     * Render HTML menu recursively starting from the specified level
     *
     * @param array $menu
     * @param int $level
     * @return string
     */
    protected function _renderMenuLevel(array $menu, $level = 0)
    {
        $result = '<ul' . (!$level ? ' id="nav"' : '') . '>';
        foreach ($menu as $item) {
            $hasChildren = !empty($item['children']);
            $cssClasses = array('level' . $level);
            if (!$level && !empty($item['active'])) {
                $cssClasses[] = 'active';
            }
            if ($hasChildren) {
                $cssClasses[] = 'parent';
            }
            if (!empty($level) && !empty($item['last'])) {
                $cssClasses[] = 'last';
            }
            $result .= '<li'
                . ($hasChildren ? ' onmouseover="Element.addClassName(this,\'over\')"' : '')
                . ($hasChildren ? ' onmouseout="Element.removeClassName(this,\'over\')"' : '')
                . ' class="' . implode(' ', $cssClasses) . '">'
                . '<a'
                . ' href="' . $item['url'] . '"'
                . (!empty($item['title']) ? ' title="' . $item['title'] . '"' : '')
                . (!empty($item['click']) ? ' onclick="' . $item['click'] . '"' : '')
                . ($level === 0 && !empty($item['active']) ? ' class="active"' : '')
                . '>'
                . '<span>' . Mage::helper('Mage_Backend_Helper_Data')->escapeHtml($item['label']) . '</span>'
                . '</a>'
            ;
            if ($hasChildren) {
                $result .= $this->_renderMenuLevel($item['children'], $level + 1);
            }
            $result .= '</li>';
        }
        $result .= '</ul>';
        return $result;
    }

    /**
     * Render HTML menu
     *
     * @return string
     */
    public function renderMenu()
    {
        return $this->_renderMenuLevel($this->_buildMenuArray());
    }

    /**
     * Get menu level HTML code
     *
     * @param array $menu
     * @param int $level
     * @return string
     */
    public function getMenuLevel($menu, $level = 0)
    {
        $html = '<ul ' . (!$level ? 'id="nav"' : '') . '>' . PHP_EOL;
        foreach ($menu as $item) {
            $html .= '<li ' . (!empty($item['children']) ? 'onmouseover="Element.addClassName(this,\'over\')" '
                . 'onmouseout="Element.removeClassName(this,\'over\')"' : '') . ' class="'
                . (!$level && !empty($item['active']) ? ' active' : '') . ' '
                . (!empty($item['children']) ? ' parent' : '')
                . (!empty($level) && !empty($item['last']) ? ' last' : '')
                . ' level' . $level . '"> <a href="' . $item['url'] . '" '
                . (!empty($item['title']) ? 'title="' . $item['title'] . '"' : '') . ' '
                . (!empty($item['click']) ? 'onclick="' . $item['click'] . '"' : '') . ' class="'
                . ($level === 0 && !empty($item['active']) ? 'active' : '') . '"><span>'
                . $this->escapeHtml($item['label']) . '</span></a>' . PHP_EOL;

            if (!empty($item['children'])) {
                $html .= $this->getMenuLevel($item['children'], $level + 1);
            }
            $html .= '</li>' . PHP_EOL;
        }
        $html .= '</ul>' . PHP_EOL;

        return $html;
    }

    /**
     * Check is module output enabled
     *
     * @param Varien_Simplexml_Element $child
     * @return bool
     */
    protected function _isEnabledModuleOutput(Varien_Simplexml_Element $child)
    {
        $helperName      = 'Mage_Backend_Helper_Data';
        $childAttributes = $child->attributes();
        if (isset($childAttributes['module'])) {
            $helperName  = (string)$childAttributes['module'];
        }

        return Mage::helper($helperName)->isModuleOutputEnabled();
    }
}
