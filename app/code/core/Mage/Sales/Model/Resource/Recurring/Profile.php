<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Recurring payment profiles resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Recurring_Profile extends Mage_Sales_Model_Resource_Abstract
{
    /**
     * Initialize main table and column
     *
     */
    protected function _construct()
    {
        $this->_init('sales_recurring_profile', 'profile_id');

        $this->_serializableFields = array(
            'profile_vendor_info'    => array(null, array()),
            'additional_info' => array(null, array()),

            'order_info' => array(null, array()),
            'order_item_info' => array(null, array()),
            'billing_address_info' => array(null, array()),
            'shipping_address_info' => array(null, array())
        );
    }

    /**
     * Return recurring profile child Orders Ids
     *
     *
     * @param Varien_Object $object
     * @return array
     */
    public function getChildOrderIds($object)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array(':profile_id' => $object->getId());
        $select  = $adapter->select()
            ->from(
                array('main_table' => $this->getTable('sales_recurring_profile_order')),
                array('order_id'))
            ->where('profile_id=:profile_id');

        return $adapter->fetchCol($select, $bind);
    }

    /**
     * Add order relation to recurring profile
     *
     * @param int $recurringProfileId
     * @param int $orderId
     * @return Mage_Sales_Model_Resource_Recurring_Profile
     */
    public function addOrderRelation($recurringProfileId, $orderId)
    {
        $this->_getWriteAdapter()->insert(
            $this->getTable('sales_recurring_profile_order'), array(
                'profile_id' => $recurringProfileId,
                'order_id'   => $orderId
            )
        );
        return $this;
    }
}
