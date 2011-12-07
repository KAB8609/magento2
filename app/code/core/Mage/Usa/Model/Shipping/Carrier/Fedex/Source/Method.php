<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Usa_Model_Shipping_Carrier_Fedex_Source_Method
{
    public function toOptionArray()
    {
        $fedex = Mage::getSingleton('Mage_Usa_Model_Shipping_Carrier_Fedex');
        $arr = array();
        foreach ($fedex->getCode('method') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
