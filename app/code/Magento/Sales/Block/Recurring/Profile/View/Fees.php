<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Recurring\Profile\View;

/**
 * Recurring profile view fees
 */
class Fees extends \Magento\Sales\Block\Recurring\Profile\View
{
    /**
     * Prepare fees info
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_shouldRenderInfo = true;
        $this->_addInfo(array(
            'label' => $this->_profile->getFieldLabel('currency_code'),
            'value' => $this->_profile->getCurrencyCode()
        ));
        $params = array('init_amount', 'trial_billing_amount', 'billing_amount', 'tax_amount', 'shipping_amount');
        foreach ($params as $key) {
            $value = $this->_profile->getData($key);
            if ($value) {
                $this->_addInfo(array(
                    'label' => $this->_profile->getFieldLabel($key),
                    'value' => $this->helper('Magento\Core\Helper\Data')->formatCurrency($value, false),
                    'is_amount' => true,
                ));
            }
        }
    }
}