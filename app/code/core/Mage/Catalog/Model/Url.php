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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog url model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Catalog_Model_Url
{
    /**
     * Stores configuration
     *
     * @var array
     */
    protected $_stores;

    /**
     * URL Rewrites by store_id and id_path
     *
     * @var array
     */
    protected $_rewrites;

    /**
     * Categories cache by store_id
     *
     * @var array
     */
    protected $_categories;

    /**
     * Products cache by store_id
     *
     * @var array
     */
    protected $_products;

    /**
     * URL Rewrites by store_id and request_path
     *
     * @var unknown_type
     */
    protected $_paths;

    /**
     * Load url rewrites from core_url_rewrite table
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function loadRewrites($storeId)
    {
        $rewriteCollection = Mage::getResourceModel('core/url_rewrite_collection');
        $rewriteCollection->getSelect()
            ->where("id_path like 'category/%' or id_path like 'product/%'")
            ->where("store_id=?", $storeId);
        $rewriteCollection->load();

        $this->_rewrites[$storeId] = array();
        foreach ($rewriteCollection as $rewrite) {
            // store rewrites by idPath
            $this->_rewrites[$rewrite->getStoreId()][$rewrite->getIdPath()] = $rewrite;
            // store rewrites by requestPath
            $this->_paths[$rewrite->getStoreId()][$rewrite->getRequestPath()] = $rewrite->getIdPath();
        }
        return $this;
    }

    /**
     * Get requestPath that was not used yet.
     *
     * Will try to get unique path by adding -1 -2 etc. between url_key and optional url_suffix
     *
     * @param int $storeId
     * @param string $requestPath
     * @param string $idPath
     * @return string
     */
    public function getUnusedPath($storeId, $requestPath, $idPath=null)
    {
        // repeat while supplied request_path already been used
        while (isset($this->_paths[$storeId][$requestPath])) {
            // if id_path was supplied and it matches cached request_path, continue with this request_path
            if (!is_null($idPath) && $this->_paths[$storeId][$requestPath]===$idPath) {
                break;
            }
            // retrieve url_suffix for product urls
            $productUrlSuffix = (string)$this->getStoreConfig($storeId)->catalog->seo->product_url_suffix;
            // match request_url abcdef1234(-12)(.html) pattern
            if (!preg_match('#^([0-9a-z/-]+?)(-([0-9]+))?('.preg_quote($productUrlSuffix).')?$#i', $requestPath, $m)) {
                // if doesn't match can't do much about it
                break;
            }
            // change request_path to make it unique
            $requestPath = $m[1].'-'.($m[3]+1).$m[4];
            // continue until unique request_path found
        }
        // store request_path in cache
        $this->_paths[$storeId][$requestPath] = $idPath;
        return $requestPath;
    }

    /**
     * Load Categories cache
     *
     * @param integer $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function loadCategories($storeId)
    {
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path');
        $categoryCollection->getEntity()
            ->setStore($storeId);
        $categoryCollection->load();

        $this->_categories = array();
        foreach ($categoryCollection as $category) {
            $this->_categories[$storeId][$category->getId()] = $category;
        }

        foreach ($this->_categories[$storeId] as $categoryId=>$category) {
            $parent = $this->getCategory($storeId, $category->getParentId());
            if (!$parent) {
                continue;
            }
            $children = $parent->getChildren();
            $children[$categoryId] = $category;
            $parent->setChildren($children);
        }
echo "<pre>".print_r($this->_categories,1)."</pre>";
        return $this;
    }

    /**
     * Load Products cache
     *
     * @param integer $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function loadProducts($storeId)
    {
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('url_key');
        $productCollection->getEntity()
            ->setStore($storeId);
        $productCollection->load();

        $this->_products[$storeId] = $productCollection->getItems();

        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('catalog_read');
        $productStoreTable = $resource->getTableName('catalog/product_store');
        $categoryProductTable = $resource->getTableName('catalog/category_product');

        $select = $read->select()
            ->from(array('cp'=>$categoryProductTable))
            ->join(array('ps'=>$productStoreTable), 'ps.product_id=cp.product_id', array())
            ->where('ps.store_id=?', $storeId);

        $categoryProducts = $read->fetchAll($select);
        foreach ($categoryProducts as $row) {
            $category = $this->getCategory($storeId, $row['category_id']);
            $product = $this->getProduct($storeId, $row['product_id']);
            if (!$category || !$product) {
                continue;
            }
            $products = $category->getProducts();
            $products[$product->getId()] = $product;
            $category->setProducts($products);

            $categories = $product->getCategories();
            $categories[$category->getId()] = $category;
            $product->setCategories($categories);
        }

        return $this;
    }

    /**
     * Get store config simplexml node
     *
     * @param integer $storeId
     * @return Mage_Core_Model_Config_Element
     */
    public function getStoreConfig($storeId=null)
    {
        if (!$this->_stores) {
            foreach (Mage::getConfig()->getNode('stores')->children() as $storeNode) {
                $sId = (int)$storeNode->system->store->id;
                $rId = $storeNode;
                if ($sId==0) {
                    continue;
                }
                $this->_stores[$sId] = $rId;
            }
        }
        if (is_null($storeId)) {
            return $this->_stores;
        }

        return isset($this->_stores[$storeId]) ? $this->_stores[$storeId] : null;
    }

    /**
     * Get root category id for the store
     *
     * @param integer $storeId
     * @return integer|array
     */
    public function getRootId($storeId=null)
    {
        if (is_null($storeId)) {
            $rootIds = array();
            foreach ($this->getStoreConfig() as $storeId=>$storeNode) {
                $rootIds[$storeId] = (int)$storeNode->catalog->category->root_id;
            }
            return $rootIds;
        } else {
            return (int)$this->getStoreConfig($storeId)->catalog->category->root_id;
        }
    }

    /**
     * Get rewrite object by id_path
     *
     * @param integer $storeId
     * @param string $idPath
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getRewrite($storeId, $idPath=null)
    {
        if (is_null($idPath)) {
            return isset($this->_rewrites[$storeId]) ? $this->_rewrites[$storeId] : null;
        }
        if (!isset($this->_rewrites[$storeId][$idPath])) {
            $rewrite = Mage::getModel('core/url_rewrite')->setStoreId($storeId)->loadByIdPath($idPath);
            $this->_rewrites[$storeId][$idPath] = $rewrite->getId() ? $rewrite : false;
        }
        return $this->_rewrites[$storeId][$idPath];
    }
    /**
     * Get category object
     *
     * @param integer $storeId
     * @param integer|null $categoryId
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory($storeId, $categoryId=null)
    {
        if (is_null($categoryId)) {
            return isset($this->_categories[$storeId]) ? $this->_categories[$storeId] : null;
        }
        if (!isset($this->_categories[$storeId][$categoryId])) {
            $category = Mage::getModel('catalog/category')->setStoreId($storeId)->load($categoryId);
            $this->_categories[$storeId][$categoryId] = $category->getId() ? $category : false;
        }
        return $this->_categories[$storeId][$categoryId];
    }

    /**
     * Get product object
     *
     * @param integer $storeId
     * @param integer|null $productId
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct($storeId, $productId=null)
    {
        if (is_null($productId)) {
            return $this->_products[$storeId];
        }
        if (!isset($this->_products[$storeId][$productId])) {
            $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
            $this->_products[$storeId][$productId] = $product->setId() ? $product : false;
        }
        return $this->_products[$storeId][$productId];
    }

    /**
     * Refresh URL rewrites
     *
     * If $storeId is null will go over all the stores
     * If $parentId is null will start from root category id for the store
     *
     * @param integer|null $storeId
     * @param integer|null $parentId
     * @return Mage_Catalog_Model_Url
     */
    public function refreshRewrites($storeId=null, $parentId=null)
    {
        if (is_null($storeId)) {
            foreach ($this->getRootId() as $storeId=>$rootId) {
                if ($storeId==0) {
                    continue;
                }
                $this->loadRewrites($storeId);
                $this->loadCategories($storeId);
                $this->loadProducts($storeId);
                $this->refreshRewrites($storeId, $parentId);
            }
            return $this;
        }

        if (is_null($parentId)) {
            $products = $this->getProduct($storeId);
            if ($products) {
                foreach ($products as $productId=>$product) {
                    $this->refreshProductRewrites($storeId, $product);
                }
            }
            $parent = $this->getCategory($storeId, $this->getRootId($storeId));
            $parentPath = '';
        } else {
            $parent = $this->getCategory($storeId, $parentId);
            $parentPath = $parent->getUrlPath().'/';
        }
        if (!$parent) {
            return;
        }

        $categories = $parent->getChildren();
        if (is_array($categories)) {
            foreach ($categories as $categoryId=>$category) {
                $this->refreshCategoryRewrites($storeId, $category, $parentPath);
                $this->refreshRewrites($storeId, $categoryId);
            }
        }

        return $this;
    }

    /**
     * Refresh URL rewrites for a category
     *
     * @param integer $storeId
     * @param Mage_Catalog_Model_category $category
     * @param string $parentPath
     * @return Mage_Catalog_Model_Url
     */
    public function refreshCategoryRewrites($storeId, $category, $parentPath=null)
    {
        if (''==$category->getUrlKey()) {
            return $this;
        }
        if (is_null($parentPath)) {
            $parent = $this->getCategory($storeId, $category->getParentId());
            $parentPath = $parent->getUrlPath();
        }
        $idPath = 'category/'.$category->getId();
        $targetPath = 'catalog/category/view/id/'.$category->getId();
        $categoryPath = $parentPath.$category->getUrlKey();
        $categoryPath = $this->getUnusedPath($storeId, $categoryPath, $idPath);
        $update = false;
        $rewrite = $this->getRewrite($storeId, $idPath);
        if ($rewrite) {
            $update = $rewrite->getRequestPath() !== $categoryPath;
        } else {
            $rewrite = Mage::getModel('core/url_rewrite')
                ->setStoreId($storeId)
                ->setIdPath($idPath)
                ->setTargetPath($targetPath);
            $update = true;
        }
        if ($update) {
            $category->setUrlPath($categoryPath)->save();
            $rewrite->setRequestPath($categoryPath)->save();
        }

        $products = $category->getProducts();
        if ($products) {
            foreach ($products as $productId=>$product) {
                $this->refreshProductRewrites($storeId, $product, $category);
            }
        }
        return $this;
    }

    /**
     * Refresh URL rewrites for a product
     *
     * @param integer $storeId
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Url
     */
    public function refreshProductRewrites($storeId, $product, $category=null)
    {
        if (''==$product->getUrlKey()) {
            return $this;
        }

        $idPath = 'product/'.$product->getId();
        $targetPath = 'catalog/product/view/id/'.$product->getId();
        $productPath = '';

        if ($category instanceof Mage_Catalog_Model_Category) {
            $idPath .= '/'.$category->getId();
            $targetPath .= '/category/'.$category->getId();
            $productPath = $category->getUrlPath().'/';
        }

        $productUrlSuffix = (string)$this->getStoreConfig($storeId)->catalog->seo->product_url_suffix;
        $productPath .= $product->getUrlKey().$productUrlSuffix;
        $productPath = $this->getUnusedPath($storeId, $productPath, $idPath);

        $update = false;
        $rewrite = $this->getRewrite($storeId, $idPath);
        if ($rewrite) {
            $update = $rewrite->getRequestPath() !== $productPath;
        } else {
            $rewrite = Mage::getModel('core/url_rewrite')
                ->setStoreId($storeId)
                ->setIdPath($idPath)
                ->setTargetPath($targetPath);
            $update = true;
        }
        if ($update) {
            $rewrite->setRequestPath($productPath)->save();
        }
        if (true===$category) {
            foreach ($product->getCategories() as $category) {
                $this->refreshProductRewrites($storeId, $product, $category);
            }
        }
        return $this;
    }
}