<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Firstdata Payment Action Dropdown source
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
class Magento_Pbridge_Model_Source_Firstdata_PaymentAction
{
    /**
     * Return list of available payment actions for gateway
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => Mage::helper('Magento_Pbridge_Helper_Data')->__('Authorize Only')
            ),
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('Magento_Pbridge_Helper_Data')->__('Authorize and Capture')
            ),
        );
    }
}
