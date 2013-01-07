<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin role data grid collection
 *
 * @category    Mage
 * @package     Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Model_Resource_Role_Grid_Collection extends Mage_User_Model_Resource_Role_Collection
{
    /**
     * Prepare select for load
     *
     * @param Varien_Db_Select $select
     * @return string
     */
    protected function _prepareSelect(Varien_Db_Select $select)
    {
        $this->addFieldToFilter('role_type', 'G');
        return parent::_prepareSelect($select);
    }
}