<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml permission tab on category page
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
class Magento_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions
    extends Magento_Adminhtml_Block_Catalog_Category_Abstract
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_template = 'catalog/category/tab/permissions.phtml';

    /**
     * Prepare layout
     *
     * @return Magento_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions
     */
    protected function _prepareLayout()
    {
        $this->addChild('row', 'Magento_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions_Row');

        $this->addChild('add_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => $this->helper('Magento_CatalogPermissions_Helper_Data')->__('New Permission'),
            'class' => 'add' . ($this->isReadonly() ? ' disabled' : ''),
            'type'  => 'button',
            'disabled' => $this->isReadonly()
        ));

        return parent::_prepareLayout();
    }

    /**
     * Retrieve block config as JSON
     *
     * @return string
     */
    public function getConfigJson()
    {
        $config = array(
            'row' => $this->getChildHtml('row'),
            'duplicate_message' => $this->helper('Magento_CatalogPermissions_Helper_Data')->__('You already have a permission with this scope.'),
            'permissions'  => array()
        );

        if ($this->getCategoryId()) {
            foreach ($this->getPermissionCollection() as $permission) {
                $config['permissions']['permission' . $permission->getId()] = $permission->getData();
            }
        }

        $config['single_mode']  = Mage::app()->hasSingleStore();
        $config['website_id']   = Mage::app()->getStore(true)->getWebsiteId();
        $config['parent_vals']  = $this->getParentPermissions();

        $config['use_parent_allow'] = Mage::helper('Magento_CatalogPermissions_Helper_Data')->__('(Allow)');
        $config['use_parent_deny'] = Mage::helper('Magento_CatalogPermissions_Helper_Data')->__('(Deny)');
        //$config['use_parent_config'] = Mage::helper('Magento_CatalogPermissions_Helper_Data')->__('(Config)');
        $config['use_parent_config'] = '';

        $additionalConfig = $this->getAdditionConfigData();
        if (is_array($additionalConfig)) {
            $config = array_merge($additionalConfig, $config);
        }

        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($config);
    }

    /**
     * Retrieve permission collection
     *
     * @return Magento_CatalogPermissions_Model_Resource_Permission_Collection
     */
    public function getPermissionCollection()
    {
        if (!$this->hasData('permission_collection')) {
            $collection = Mage::getModel('Magento_CatalogPermissions_Model_Permission')
                ->getCollection()
                ->addFieldToFilter('category_id', $this->getCategoryId())
                ->setOrder('permission_id', 'asc');
            $this->setData('permisssion_collection', $collection);
        }

        return $this->getData('permisssion_collection');
    }

    /**
     * Retrieve Use Parent permissions per website and customer group
     *
     * @return array
     */
    public function getParentPermissions()
    {
        $categoryId = null;
        if ($this->getCategoryId()) {
            $categoryId = $this->getCategory()->getParentId();
        }
        // parent category
        else if ($this->getRequest()->getParam('parent')) {
            $categoryId = $this->getRequest()->getParam('parent');
        }

        $permissions = array();
        if ($categoryId) {
            $index  = Mage::getModel('Magento_CatalogPermissions_Model_Permission_Index')
                ->getIndexForCategory($categoryId, null, null);
            foreach ($index as $row) {
                $permissionKey = $row['website_id'] . '_' . $row['customer_group_id'];
                $permissions[$permissionKey] = array(
                    'category'  => $row['grant_catalog_category_view'],
                    'product'   => $row['grant_catalog_product_price'],
                    'checkout'  => $row['grant_checkout_items']
                );
            }
        }

        $websites = Mage::app()->getWebsites(false);
        $groups   = Mage::getModel('Magento_Customer_Model_Group')->getCollection()->getAllIds();

        /* @var $helper Magento_CatalogPermissions_Helper_Data */
        $helper   = Mage::helper('Magento_CatalogPermissions_Helper_Data');

        $parent = (string)Magento_CatalogPermissions_Model_Permission::PERMISSION_PARENT;
        $allow  = (string)Magento_CatalogPermissions_Model_Permission::PERMISSION_ALLOW;
        $deny   = (string)Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY;

        foreach ($groups as $groupId) {
            foreach ($websites as $website) {
                /* @var $website Magento_Core_Model_Website */
                $websiteId = $website->getId();

                $store = $website->getDefaultStore();
                $category = $helper->isAllowedCategoryView($store, $groupId);
                $product  = $helper->isAllowedProductPrice($store, $groupId);
                $checkout = $helper->isAllowedCheckoutItems($store, $groupId);

                $permissionKey = $websiteId . '_' . $groupId;
                if (!isset($permissions[$permissionKey])) {
                    $permissions[$permissionKey] = array(
                        'category'  => $category ? $allow : $deny,
                        'product'   => $product ? $allow : $deny,
                        'checkout'  => $checkout ? $allow : $deny
                    );
                } else {
                    // validate and rewrite parent values for exists data
                    $data = $permissions[$permissionKey];
                    $permissions[$permissionKey] = array(
                        'category'  => $data['category'] == $parent ? ($category ? $allow : $deny) : $data['category'],
                        'product'   => $data['product'] == $parent ? ($checkout ? $allow : $deny) : $data['product'],
                        'checkout'  => $data['checkout'] == $parent ? ($product ? $allow : $deny) : $data['checkout'],
                    );
                }
            }
        }

        return $permissions;
    }

    /**
     * Retrieve tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->helper('Magento_CatalogPermissions_Helper_Data')->__('Category Permissions');
    }

    /**
     * Retrieve tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->helper('Magento_CatalogPermissions_Helper_Data')->__('Category Permissions');
    }

    /**
     * Tab visibility
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $canShow = $this->getCanShowTab();
        if (is_null($canShow)) {
            $canShow = $this->_authorization
                ->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions');
        }
        return $canShow;
    }

    /**
     * Tab visibility
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve add button html
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Check is block readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getCategory()->getPermissionsReadonly();
    }
}
