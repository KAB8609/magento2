<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for DHL shipping methods for documentation
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Usa_Model_Shipping_Carrier_Dhl_International_Source_Method_Freenondoc
    extends Magento_Usa_Model_Shipping_Carrier_Dhl_International_Source_Method_Abstract
{
    /**
     * Carrier Product Type Indicator
     *
     * @var string $_contentType
     */
    protected $_contentType = Magento_Usa_Model_Shipping_Carrier_Dhl_International::DHL_CONTENT_TYPE_NON_DOC;

    /**
     * Show 'none' in methods list or not;
     *
     * @var bool
     */
    protected $_noneMethod = true;
}