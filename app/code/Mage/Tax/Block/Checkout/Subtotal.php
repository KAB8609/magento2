<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Subtotal Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tax_Block_Checkout_Subtotal extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'checkout/subtotal.phtml';

    public function displayBoth()
    {
        return Mage::getSingleton('Mage_Tax_Model_Config')->displayCartSubtotalBoth($this->getStore());
    }
}