<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Compare list sidebar container
 */
class Enterprise_PageCache_Model_Container_Sidebar_Comparelist extends Enterprise_PageCache_Model_Container_Abstract
{
    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_COMPARE_LIST, '');
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return 'CONTAINER_COMPARELIST_' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $template = $this->_placeholder->getAttribute('template');

        $block = Mage::app()->getLayout()->createBlock('Mage_Catalog_Block_Product_Compare_List');
        $block->setTemplate($template);
        $block->setLayout(Mage::app()->getLayout());

        return $block->toHtml();
    }
}
