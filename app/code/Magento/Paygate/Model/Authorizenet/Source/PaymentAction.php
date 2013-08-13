<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * Authorizenet Payment Action Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paygate_Model_Authorizenet_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Paygate_Model_Authorizenet::ACTION_AUTHORIZE,
                'label' => Mage::helper('Magento_Paygate_Helper_Data')->__('Authorize Only')
            ),
            array(
                'value' => Magento_Paygate_Model_Authorizenet::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('Magento_Paygate_Helper_Data')->__('Authorize and Capture')
            ),
        );
    }
}
