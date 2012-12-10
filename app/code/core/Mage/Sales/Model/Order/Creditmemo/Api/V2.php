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
 * Credit memo API
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Creditmemo_Api_V2 extends Mage_Sales_Model_Order_Creditmemo_Api
{
    /**
     * Prepare data
     *
     * @param null|object $data
     * @return array
     */
    protected function _prepareCreateData($data)
    {
        // convert data object to array, if it's null turn it into empty array
        $data = (isset($data) and is_object($data)) ? get_object_vars($data) : array();
        // convert qtys object to array
        if (isset($data['qtys']) && count($data['qtys'])) {
            $qtysArray = array();
            foreach ($data['qtys'] as &$item) {
                if (isset($item->order_item_id) && isset($item->qty)) {
                    $qtysArray[$item->order_item_id] = $item->qty;
                }
            }
            $data['qtys'] = $qtysArray;
        }
        return $data;
    }
}
