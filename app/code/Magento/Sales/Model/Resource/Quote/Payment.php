<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote payment resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Payment extends Magento_Sales_Model_Resource_Abstract
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields   = array(
        'additional_information' => array(null, array())
    );

    /**
     * Main table and field initialization
     *
     */
    protected function _construct()
    {
        $this->_converter = Mage::getSingleton('Magento_Sales_Model_Payment_Method_Converter');
        $this->_init('sales_flat_quote_payment', 'payment_id');
    }
}