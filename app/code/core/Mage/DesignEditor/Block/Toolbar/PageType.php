<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page types navigation control
 */
class Mage_DesignEditor_Block_Toolbar_PageType extends Mage_Core_Block_Template
{
    /**
     * @var string|false
     */
    protected $_selectedPageType;

    /**
     * Recursively render each level of the page types hierarchy as an HTML list
     *
     * @param array $pageTypes
     * @return string
     */
    protected function _renderPageTypes(array $pageTypes)
    {
        if (!$pageTypes) {
            return '';
        }
        $result = '<ul>';
        foreach ($pageTypes as $name => $info) {
            $result .= '<li rel="' . $name . '">';
            $result .= '<a href="' . $this->getUrl('design/editor/page', array('page_type' => $name)) . '">';
            $result .= $this->escapeHtml($info['label']);
            $result .= '</a>';
            $result .= $this->_renderPageTypes($info['children']);
            $result .= '</li>';
        }
        $result .= '</ul>';
        return $result;
    }

    /**
     * Render page types hierarchy as an HTML list
     *
     * @return string
     */
    public function renderPageTypes()
    {
        return $this->_renderPageTypes($this->getLayout()->getUpdate()->getPageTypesHierarchy());
    }

    /**
     * Retrieve the name of the currently selected page type
     *
     * @return string|false
     */
    public function getSelectedPageType()
    {
        if ($this->_selectedPageType === null) {
            $pageHandles = $this->getLayout()->getUpdate()->getPageHandles();
            $this->_selectedPageType = end($pageHandles);
        }
        return $this->_selectedPageType;
    }

    /**
     * Set the name of the currently selected page type
     *
     * @param string $name Page type name
     */
    public function setSelectedPageType($name)
    {
        $this->_selectedPageType = $name;
    }
}