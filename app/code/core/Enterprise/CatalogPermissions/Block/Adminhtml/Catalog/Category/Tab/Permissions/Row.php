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
 * Adminhtml permissions row block
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions_Row
    extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/category/tab/permissions/row.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('delete_button', $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->addData(array(
                //'label' => $this->helper('Enterprise_CatalogPermissions_Helper_Data')->__('Remove Permission'),
                'class' => 'delete' . ($this->isReadonly() ? ' disabled' : ''),
                'disabled' => $this->isReadonly(),
                'type'  => 'button',
                'id'    => '{{html_id}}_delete_button'
            ))
        );

        return parent::_prepareLayout();
    }

    /**
     * Check edit by websites
     *
     * @return boolean
     */
    public function canEditWebsites()
    {
        return !Mage::app()->isSingleStoreMode();
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

    public function getDefaultWebsiteId()
    {
        return Mage::app()->getStore(true)->getWebsiteId();
    }

    /**
     * Retrieve list of permission grants
     *
     * @return array
     */
    public function getGrants()
    {
        return array(
            'grant_catalog_category_view' => $this->helper('Enterprise_CatalogPermissions_Helper_Data')->__('Browsing Category'),
            'grant_catalog_product_price' => $this->helper('Enterprise_CatalogPermissions_Helper_Data')->__('Display Product Prices'),
            'grant_checkout_items' => $this->helper('Enterprise_CatalogPermissions_Helper_Data')->__('Add to Cart')
        );
    }

    /**
     * Retrieve field class name
     *
     * @param string $fieldId
     * @return string
     */
    public function getFieldClassName($fieldId)
    {
        return strtr($fieldId, '_', '-') . '-value';
    }

    /**
     * Retrieve websites collection
     *
     * @return Mage_Core_Model_Resource_Website_Collection
     */
    public function getWebsiteCollection()
    {
        if (!$this->hasData('website_collection')) {
            $collection = Mage::getModel('Mage_Core_Model_Website')->getCollection();
            $this->setData('website_collection', $collection);
        }

        return $this->getData('website_collection');
    }

    /**
     * Retrieve customer group collection
     *
     * @return Mage_Customer_Model_Resource_Group_Collection
     */
    public function getCustomerGroupCollection()
    {
        if (!$this->hasData('customer_group_collection')) {
            $collection = Mage::getModel('Mage_Customer_Model_Group')->getCollection();
            $this->setData('customer_group_collection', $collection);
        }

        return $this->getData('customer_group_collection');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }
}
