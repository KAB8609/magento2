<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Instance layouts chooser
 *
 * @method getArea()
 * @method getTheme()
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout extends Mage_Core_Block_Html_Select
{
    /**
     * Add necessary options
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption('', __('-- Please Select --'));
            $layoutMergeParams = array(
                'theme' => $this->_getThemeInstance($this->getTheme()),
            );
            $pageTypes = array();
            $pageTypesAll = $this->_getLayoutMerge($layoutMergeParams)->getPageHandlesHierarchy();
            foreach ($pageTypesAll as $pageTypeName => $pageTypeInfo) {
                $layoutMerge = $this->_getLayoutMerge($layoutMergeParams);
                $layoutMerge->addPageHandles(array($pageTypeName));
                $layoutMerge->load();
                if (!$layoutMerge->getContainers()) {
                    continue;
                }
                $pageTypes[$pageTypeName] = $pageTypeInfo;
            }
            $this->_addPageTypeOptions($pageTypes);
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve theme instance by its identifier
     *
     * @param int $themeId
     * @return Mage_Core_Model_Theme|null
     */
    protected function _getThemeInstance($themeId)
    {
        /** @var Mage_Core_Model_Resource_Theme_Collection $themeCollection */
        $themeCollection = Mage::getResourceModel('Mage_Core_Model_Resource_Theme_Collection');
        return $themeCollection->getItemById($themeId);
    }

    /**
     * Retrieve new layout merge model instance
     *
     * @param array $arguments
     * @return Mage_Core_Model_Layout_Merge
     */
    protected function _getLayoutMerge(array $arguments)
    {
        return Mage::getModel('Mage_Core_Model_Layout_Merge', $arguments);
    }

    /**
     * Add page types information to the options
     *
     * @param array $pageTypes
     * @param int $level
     */
    protected function _addPageTypeOptions(array $pageTypes, $level = 0)
    {
        foreach ($pageTypes as $pageTypeName => $pageTypeInfo) {
            $params = array();
            if ($pageTypeInfo['type'] == Mage_Core_Model_Layout_Merge::TYPE_FRAGMENT) {
                $params['class'] = 'fragment';
            }
            $this->addOption($pageTypeName, str_repeat('. ', $level) . $pageTypeInfo['label'], $params);
            $this->_addPageTypeOptions($pageTypeInfo['children'], $level + 1);
        }
    }
}
