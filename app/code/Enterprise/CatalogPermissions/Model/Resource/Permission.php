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
 * Permission resource model
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CatalogPermissions_Model_Resource_Permission extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_catalogpermissions', 'permission_id');
    }

    /**
     * Initialize unique scope for permission
     *
     */
    protected function _initUniqueFields()
    {
        parent::_initUniqueFields();
        $this->_uniqueFields[] = array(
            'field' => array('category_id', 'website_id', 'customer_group_id'),
            'title' => Mage::helper('Enterprise_CatalogPermissions_Helper_Data')->__('Permission with the same scope')
        );
    }
}
