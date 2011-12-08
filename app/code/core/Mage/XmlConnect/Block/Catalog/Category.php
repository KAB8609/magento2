<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category list xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Category extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $categoryXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $categoryXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element', '<category></category>');
        $categoryId     = $this->getRequest()->getParam('id', null);
        if ($categoryId === null) {
            $categoryId = Mage::app()->getStore()->getRootCategoryId();
        }

        $productsXmlObj = $productListBlock = false;
        /** @var $categoryModel Mage_Catalog_Model_Category */
        $categoryModel  = Mage::getModel('Mage_Catalog_Model_Category')->load($categoryId);
        if ($categoryModel->getId()) {
            $hasMoreProductItems = 0;
            $productListBlock = $this->getChild('product_list');
            if ($productListBlock && $categoryModel->getLevel() > 1) {
                $layer = Mage::getSingleton('Mage_Catalog_Model_Layer');
                $productsXmlObj = $productListBlock->setCategory($categoryModel)->setLayer($layer)
                    ->getProductsXmlObject();
                $hasMoreProductItems = (int)$productListBlock->getHasProductItems();
            }

            $infoBlock = $this->getChild('category_info');
            if ($infoBlock) {
                $categoryInfoXmlObj = $infoBlock->setCategory($categoryModel)->getCategoryInfoXmlObject();
                $categoryInfoXmlObj->addChild('has_more_items', $hasMoreProductItems);
                $categoryXmlObj->appendChild($categoryInfoXmlObj);
            }
        }

        $categoryCollection = $this->getCurrentChildCategories();

        // subcategories are exists
        if (sizeof($categoryCollection)) {
            $itemsXmlObj = $categoryXmlObj->addChild('items');
            foreach ($categoryCollection as $item) {
                /** @var $item Mage_Catalog_Model_Category */
                $item = Mage::getModel('Mage_Catalog_Model_Category')->load($item->getId());

                $itemXmlObj = $itemsXmlObj->addChild('item');
                $itemXmlObj->addChild('label', $categoryXmlObj->xmlentities($item->getName()));
                $itemXmlObj->addChild('entity_id', $item->getId());
                $itemXmlObj->addChild('content_type', $item->hasChildren() ? 'categories' : 'products');
                if (!is_null($categoryId)) {
                    $itemXmlObj->addChild('parent_id', $item->getParentId());
                }
                $icon = Mage::helper('Mage_XmlConnect_Helper_Catalog_Category_Image')->initialize($item, 'thumbnail')
                    ->resize(Mage::helper('Mage_XmlConnect_Helper_Image')->getImageSizeForContent('category'));

                $iconXml = $itemXmlObj->addChild('icon', $icon);

                $file = Mage::helper('Mage_XmlConnect_Helper_Data')->urlToPath($icon);
                $iconXml->addAttribute('modification_time', filemtime($file));
            }
        }

        if ($productListBlock && $productsXmlObj) {
            $categoryXmlObj->appendChild($productsXmlObj);
        }
        return $categoryXmlObj->asNiceXml();
    }

    /**
     * Retrieve child categories of current category
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getCurrentChildCategories()
    {
        $layer = Mage::getSingleton('Mage_Catalog_Model_Layer');
        $category   = $layer->getCurrentCategory();
        /* @var $category Mage_Catalog_Model_Category */
        $categories = $category->getChildrenCategories();
        $productCollection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Collection');
        $layer->prepareProductCollection($productCollection);
        $productCollection->addCountToCategories($categories);
        return $categories;
    }
}
