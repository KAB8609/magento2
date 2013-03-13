<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Shipping_Model_Rate_Result_Error extends Mage_Shipping_Model_Rate_Result_Abstract
{

    public function getErrorMessage()
    {
        if (!$this->getData('error_message')) {
            $this->setData('error_message', Mage::helper('Mage_Shipping_Helper_Data')->__('This shipping method is currently unavailable. If you would like to ship using this shipping method, please contact us.'));
        }
        return $this->getData('error_message');
    }
}
