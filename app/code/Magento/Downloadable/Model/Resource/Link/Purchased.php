<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Downloadable Product link purchased resource model
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_Resource_Link_Purchased extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Magento class constructor
     *
     */
    protected function _construct()
    {
        $this->_init('downloadable_link_purchased', 'purchased_id');
    }
}