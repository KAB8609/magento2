<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Unitofmeasure
{
    public function toOptionArray()
    {
        $unitArr = Mage::getSingleton('Mage_Usa_Model_Shipping_Carrier_Ups')->getCode('unit_of_measure');
        $returnArr = array();
        foreach ($unitArr as $key => $val){
            $returnArr[] = array('value'=>$key,'label'=>$key);
        }
        return $returnArr;
    }
}
