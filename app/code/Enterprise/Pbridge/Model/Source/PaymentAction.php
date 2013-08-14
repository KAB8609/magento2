<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment Action Dropdown source
 */
class Enterprise_Pbridge_Model_Source_PaymentAction
{
    /**
     * Return list of available payment actions for gateway
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Authorization')),
            array('value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Sale')),
        );
    }
}

