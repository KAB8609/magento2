<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Placeholder container for catalog top navigation block
 */
class Magento_FullPageCache_Model_Container_Catalognavigation extends Magento_FullPageCache_Model_Container_Abstract
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_FullPageCache_Model_Cache $fpcCache
     * @param Magento_FullPageCache_Model_Container_Placeholder $placeholder
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_FullPageCache_Model_Cache $fpcCache,
        Magento_FullPageCache_Model_Container_Placeholder $placeholder,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($fpcCache, $placeholder, $coreStoreConfig);
    }

    /**
     * @return string
     */
    protected function _getBlockCacheId()
    {
        return $this->_placeholder->getAttribute('short_cache_id');
    }

    /**
     * @return string
     */
    protected function _getCategoryCacheId()
    {
        $shortCacheId = $this->_placeholder->getAttribute('short_cache_id');
        $categoryPath = $this->_placeholder->getAttribute('category_path');
        $categoryId = $this->_getCategoryId();
        if (!$shortCacheId || !$categoryPath) {
            return false;
        }
        return $shortCacheId . '_' . $categoryPath . ($categoryId ? ('_' . $categoryId) : '');
    }

    /**
     * Generate placeholder content before application was initialized and apply to page content if possible
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        $blockCacheId = $this->_getBlockCacheId();
        $categoryCacheId = $this->_getCategoryCacheId();
        if ($blockCacheId && $categoryCacheId) {
            $blockContent = $this->_loadCache($blockCacheId);
            $categoryUniqueClasses = $this->_loadCache($categoryCacheId);
            if ($blockContent !== false && $categoryUniqueClasses !== false) {
                if ($categoryUniqueClasses != '') {
                    $regexp = '';
                    foreach (explode(' ', $categoryUniqueClasses) as $categoryUniqueClass) {
                        $regexp .= ($regexp ? '|' : '') . preg_quote($categoryUniqueClass);
                    }
                    $blockContent = preg_replace('/(?<=\s|")(' . $regexp . ')(?=\s|")/u', '$1 active', $blockContent);
                }
                $this->_applyToContent($content, $blockContent);
                return true;
            }
        }
        return false;
    }

    /**
     * Save rendered block content to cache storage
     *
     * @param string $blockContent
     * @return Magento_FullPageCache_Model_Container_Abstract
     */
    public function saveCache($blockContent)
    {
        $blockCacheId = $this->_getBlockCacheId();
        if ($blockCacheId) {
            $categoryCacheId = $this->_getCategoryCacheId();
            if ($categoryCacheId) {
                $categoryUniqueClasses = '';
                $classes = array();
                $classesCount = preg_match_all('/(?<=\s)class="(.*?active.*?)"/u', $blockContent, $classes);
                for ($i = 0; $i < $classesCount; $i++) {
                    $classAttribute = $classes[0][$i];
                    $classValue = $classes[1][$i];
                    $classInactive = preg_replace('/\s+active|active\s+|active/', '', $classAttribute);
                    $blockContent = str_replace($classAttribute, $classInactive, $blockContent);
                    $matches = array();
                    if (preg_match('/(?<=\s|^)nav-.+?(?=\s|$)/', $classValue, $matches)) {
                        $categoryUniqueClasses .= ($categoryUniqueClasses ? ' ' : '') . $matches[0];
                    }
                }
                $this->_saveCache($categoryUniqueClasses, $categoryCacheId);
            }
            if (!$this->_fpcCache->getFrontend()->test($blockCacheId)) {
                $this->_saveCache($blockContent, $blockCacheId);
            }
        }
        return $this;
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();

        $categoryId = $this->_getCategoryId();
        if (!$this->_coreRegistry->registry('current_category') && $categoryId) {
            $category = Mage::getModel('Magento_Catalog_Model_Category')->load($categoryId);
            $this->_coreRegistry->register('current_category', $category);
        }

        Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

        return $block->toHtml();
    }
}
