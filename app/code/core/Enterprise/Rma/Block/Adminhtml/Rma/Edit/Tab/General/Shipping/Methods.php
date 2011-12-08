<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shipping_Methods extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        if (Mage::registry('current_rma')) {
            $this->setShippingMethods(Mage::registry('current_rma')->getShippingMethods());
        }
    }

    public function getShippingPrice($price)
    {
        return Mage::registry('current_rma')
            ->getStore()
            ->convertPrice(
                Mage::helper('Mage_Tax_Helper_Data')->getShippingPrice(
                    $price
                ),
                true,
                false
            )
        ;
    }

    public function jsonData($method)
    {
        $data = array();
        $data['CarrierTitle']   = $method->getCarrierTitle();
        $data['MethodTitle']    = $method->getMethodTitle();
        $data['Price']          = $this->getShippingPrice($method->getPrice());
        $data['PriceOriginal']  = $method->getPrice();
        $data['Code']           = $method->getCode();

        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($data);
    }
}
