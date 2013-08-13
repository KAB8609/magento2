<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for Shippers Request Type
 *
 * @category   Mage
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Usa_Model_Shipping_Carrier_Abstract_Source_Requesttype
{
    /**
     * Returns array to be used in packages request type on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('Mage_Shipping_Helper_Data')->__('Divide to equal weight (one request)')),
            array('value' => 1, 'label' => Mage::helper('Mage_Shipping_Helper_Data')->__('Use origin weight (few requests)')),
        );
    }
}
