<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin role users collection
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Model_Resource_Role_User_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_User_Model_User', 'Magento_User_Model_Resource_User');
    }

    /**
     * Initialize select
     *
     * @return Magento_User_Model_Resource_Role_User_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->where("user_id > 0");

        return $this;
    }
}