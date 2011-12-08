<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml observer
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Model_Adminhtml_Observer
{
    const FORM_SELECT_ALL_VALUES = -1;

    protected $_indexQueue = array();
    protected $_indexProductQueue = array();

    /**
     * Check permissions availability for current category
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function checkCategoryPermissions(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();
        /* @var $category Mage_Catalog_Model_Category */
        $helper = Mage::helper('Enterprise_CatalogPermissions_Helper_Data');
        if (!$helper->isAllowedCategory($category) && $category->hasData('permissions')) {
            $category->unsetData('permissions');
        }

        return $this;
    }

    /**
     * Save category permissions on category after save event
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function saveCategoryPermissions(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Enterprise_CatalogPermissions_Helper_Data')->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();
        /* @var $category Mage_Catalog_Model_Category */
        if ($category->hasData('permissions') && is_array($category->getData('permissions'))
            && Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('catalog/enterprise_catalogpermissions')) {
            foreach ($category->getData('permissions') as $data) {
                $permission = Mage::getModel('Enterprise_CatalogPermissions_Model_Permission');
                if (!empty($data['id'])) {
                    $permission->load($data['id']);
                }

                if (!empty($data['_deleted'])) {
                    if ($permission->getId()) {
                        $permission->delete();
                    }
                    continue;
                }

                if ($data['website_id'] == self::FORM_SELECT_ALL_VALUES) {
                    $data['website_id'] = null;
                }

                if ($data['customer_group_id'] == self::FORM_SELECT_ALL_VALUES) {
                    $data['customer_group_id'] = null;
                }

                $permission->addData($data);
                $categoryViewPermission = $permission->getGrantCatalogCategoryView();
                if (Enterprise_CatalogPermissions_Model_Permission::PERMISSION_DENY == $categoryViewPermission) {
                    $permission->setGrantCatalogProductPrice(
                        Enterprise_CatalogPermissions_Model_Permission::PERMISSION_DENY
                    );
                }

                $productPricePermission = $permission->getGrantCatalogProductPrice();
                if (Enterprise_CatalogPermissions_Model_Permission::PERMISSION_DENY == $productPricePermission) {
                    $permission->setGrantCheckoutItems(Enterprise_CatalogPermissions_Model_Permission::PERMISSION_DENY);
                }
                $permission->setCategoryId($category->getId());
                $permission->save();
            }
            $this->_indexQueue[] = $category->getPath();
        }
        return $this;
    }

    /**
     * Reindex category permissions on category move event
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexCategoryPermissionOnMove(Varien_Event_Observer $observer)
    {
        $category = Mage::getModel('Mage_Catalog_Model_Category')
            ->load($observer->getEvent()->getCategoryId());
        $this->_indexQueue[] = $category->getPath();
        return $this;
    }

    /**
     * Reindex permissions in queue on postdipatch
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexPermissions()
    {
        if (!empty($this->_indexQueue)) {
            /** @var $indexer Mage_Index_Model_Indexer */
            $indexer = Mage::getSingleton('Mage_Index_Model_Indexer');
            foreach ($this->_indexQueue as $item) {
                $indexer->logEvent(
                    new Varien_Object(array('id' => $item)),
                    Enterprise_CatalogPermissions_Model_Permission_Index::ENTITY_CATEGORY,
                    Enterprise_CatalogPermissions_Model_Permission_Index::EVENT_TYPE_REINDEX_PRODUCTS
                );
            }
            $this->_indexQueue = array();
            $indexer->indexEvents(
                Enterprise_CatalogPermissions_Model_Permission_Index::ENTITY_CATEGORY,
                Enterprise_CatalogPermissions_Model_Permission_Index::EVENT_TYPE_REINDEX_PRODUCTS
            );
            Mage::app()->cleanCache(array(Mage_Catalog_Model_Category::CACHE_TAG));
        }

        if (!empty($this->_indexProductQueue)) {
            /** @var $indexer Mage_Index_Model_Indexer */
            $indexer = Mage::getSingleton('Mage_Index_Model_Indexer');
            foreach ($this->_indexProductQueue as $item) {
                $indexer->logEvent(
                    new Varien_Object(array('id' => $item)),
                    Enterprise_CatalogPermissions_Model_Permission_Index::ENTITY_PRODUCT,
                    Enterprise_CatalogPermissions_Model_Permission_Index::EVENT_TYPE_REINDEX_PRODUCTS
                );
            }
            $indexer->indexEvents(
                Enterprise_CatalogPermissions_Model_Permission_Index::ENTITY_PRODUCT,
                Enterprise_CatalogPermissions_Model_Permission_Index::EVENT_TYPE_REINDEX_PRODUCTS
            );
            $this->_indexProductQueue = array();
        }

        return $this;
    }

    /**
     * Refresh category related cache on catalog permissions config save
     *
     * @return Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function cleanCacheOnConfigChange()
    {
        Mage::app()->cleanCache(array(Mage_Catalog_Model_Category::CACHE_TAG));
        Mage::getSingleton('Mage_Index_Model_Indexer')->processEntityAction(
            new Varien_Object(),
            Enterprise_CatalogPermissions_Model_Permission_Index::ENTITY_CONFIG,
            Mage_Index_Model_Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Rebuild index for products
     *
     * @return  Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexProducts()
    {
        $this->_indexProductQueue[] = null;
        return $this;
    }

    /**
     * Rebuild index
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindex()
    {
        $this->_indexQueue[] = '1';
        return $this;
    }

    /**
     * Rebuild index after product assigned websites
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexAfterProductAssignedWebsite(Varien_Event_Observer $observer)
    {
        $productIds = $observer->getEvent()->getProducts();
        $this->_indexProductQueue = array_merge($this->_indexProductQueue, $productIds);
        return $this;
    }


    /**
     * Save product permission index
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function saveProductPermissionIndex(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_indexProductQueue[] = $product->getId();
        return $this;
    }

    /**
     * Add permission tab on category edit page
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function addCategoryPermissionTab(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Enterprise_CatalogPermissions_Helper_Data')->isEnabled()) {
            return $this;
        }
        if (!Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('catalog/enterprise_catalogpermissions')) {
            return $this;
        }

        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs Mage_Adminhtml_Block_Catalog_Category_Tabs */

        //if (Mage::helper('Enterprise_CatalogPermissions_Helper_Data')->isAllowedCategory($tabs->getCategory())) {
            $tabs->addTab(
                'permissions',
                'Enterprise_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions'
            );
        //}

        return $this;
    }

    /**
     * Apply categories and products permissions after reindex category products
     *
     * @param Varien_Event_Observer $observer
     */
    public function applyPermissionsAfterReindex(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('Mage_Index_Model_Indexer')->getProcessByCode('catalogpermissions')->reindexEverything();
    }
}
