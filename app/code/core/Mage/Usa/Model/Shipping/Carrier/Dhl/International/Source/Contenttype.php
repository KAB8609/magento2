<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for DHL Content Type
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_International_Source_Contenttype
{
    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('label' => Mage::helper('Mage_Usa_Helper_Data')->__('Documents'),
                'value' => Mage_Usa_Model_Shipping_Carrier_Dhl_International::DHL_CONTENT_TYPE_DOC),
            array('label' => Mage::helper('Mage_Usa_Helper_Data')->__('Non documents'),
                'value' => Mage_Usa_Model_Shipping_Carrier_Dhl_International::DHL_CONTENT_TYPE_NON_DOC),
        );
    }
}
