<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Event adminhtml data helper
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Helper_Adminhtml_Event extends Mage_Core_Helper_Abstract
{
    /**
     * Categories first and second level for admin
     *
     * @var Magento_Data_Tree_Node
     */
    protected $_categories = null;

    /**
     * List of category ids that already in events
     *
     * @var array
     */
    protected $_inEventCategoryIds = null;

    /**
     * Return first and second level categories
     *
     * @return Magento_Data_Tree_Node
     */
    public function getCategories()
    {
        if ($this->_categories === null) {
            $tree = Mage::getModel('Mage_Catalog_Model_Category')->getTreeModel();
            /** @var $tree Mage_Catalog_Model_Resource_Category_Tree */
            $tree->load(null, 2); // Load only to second level.
            $tree->addCollectionData(null, 'position');
            $this->_categories = $tree->getNodeById(Mage_Catalog_Model_Category::TREE_ROOT_ID)->getChildren();
        }
        return $this->_categories;
    }

    /**
     * Return first and second level categories for dropdown options
     *
     * @return array
     */
    public function getCategoriesOptions($without = array(), $emptyOption = false)
    {
        $result = array();
        foreach ($this->getCategories() as $category) {
            if (! in_array($category->getId(), $without)) {
                $result[] = $this->_treeNodeToOption($category, $without);
            }
        }

        if ($emptyOption) {
            array_unshift($result, array(
                'label' => '' , 'value' => ''
            ));
        }
        return $result;
    }

    /**
     * Convert tree node to dropdown option
     *
     * @return array
     */
    protected function _treeNodeToOption(Magento_Data_Tree_Node $node, $without)
    {

        $option = array();
        $option['label'] = $node->getName();
        if ($node->getLevel() < 2) {
            $option['value'] = array();
            foreach ($node->getChildren() as $childNode) {
                if (!in_array($childNode->getId(), $without)) {
                    $option['value'][] = $this->_treeNodeToOption($childNode, $without);
                }
            }
        } else {
            $option['value'] = $node->getId();
        }
        return $option;
    }

    /**
     * Search category in categories tree
     *
     * @param array $categories
     * @param int $categoryId
     * @return Magento_Data_Tree_Node|boolean
     */
    public function searchInCategories($categories, $categoryId)
    {

        foreach ($categories as $category) {
            if ($category->getId() == $categoryId) {
                return $category;
            } elseif ($category->hasChildren() && ($foundCategory = $this->searchInCategories($category->getChildren(), $categoryId))) {
                return $foundCategory;
            }
        }
        return false;
    }

    /**
     * Return list of category ids that already in events
     *
     * @return array
     */
    public function getInEventCategoryIds()
    {

        if ($this->_inEventCategoryIds === null) {
            $collection = Mage::getModel('Enterprise_CatalogEvent_Model_Event')->getCollection();
            $this->_inEventCategoryIds = $collection->getColumnValues('category_id');
        }
        return $this->_inEventCategoryIds;
    }
}
