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
 * Source model for DHL shipping methods for documentation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_International_Source_Method_Size
{
    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $unitArr = Mage::getSingleton('Mage_Usa_Model_Shipping_Carrier_Dhl_International')->getCode('size');

        $returnArr = array();
        foreach ($unitArr as $key => $val) {
            $returnArr[] = array('value' => $key, 'label' => $val);
        }
        return $returnArr;
    }
}
