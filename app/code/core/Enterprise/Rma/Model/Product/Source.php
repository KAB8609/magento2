<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source Model of Product's Attribute Enable RMA
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Model_Product_Source extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{
    /**
     * XML configuration path allow RMA on product level
     */
    const XML_PATH_PRODUCTS_ALLOWED = 'sales/enterprise_rma/enabled_on_product';

    /**
     * Constants - attribute value
     */
    const ATTRIBUTE_ENABLE_RMA_YES = 1;
    const ATTRIBUTE_ENABLE_RMA_NO = 0;
    const ATTRIBUTE_ENABLE_RMA_USE_CONFIG = 2;

    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('Enterprise_Rma_Helper_Data')->__('Yes'),
                    'value' => self::ATTRIBUTE_ENABLE_RMA_YES
                ),
                array(
                    'label' => Mage::helper('Enterprise_Rma_Helper_Data')->__('No'),
                    'value' => self::ATTRIBUTE_ENABLE_RMA_NO
                ),
                array(
                    'label' => Mage::helper('Enterprise_Rma_Helper_Data')->__('Use config'),
                    'value' => self::ATTRIBUTE_ENABLE_RMA_USE_CONFIG
                )
            );
        }
        return $this->_options;
    }
}
