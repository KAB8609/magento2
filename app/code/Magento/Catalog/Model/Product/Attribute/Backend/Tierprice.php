<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product tier price backend attribute model
 *
 * @category   Mage
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Tierprice
    extends Magento_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract
{
    /**
     * Retrieve resource instance
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice');
    }

    /**
     * Retrieve websites rates and base currency codes
     *
     * @deprecated since 1.12.0.0
     * @return array
     */
    public function _getWebsiteRates()
    {
        return $this->_getWebsiteCurrencyRates();
    }

    /**
     * Add price qty to unique fields
     *
     * @param array $objectArray
     * @return array
     */
    protected function _getAdditionalUniqueFields($objectArray)
    {
        $uniqueFields = parent::_getAdditionalUniqueFields($objectArray);
        $uniqueFields['qty'] = $objectArray['price_qty'] * 1;
        return $uniqueFields;
    }

    /**
     * Error message when duplicates
     *
     * @return string
     */
    protected function _getDuplicateErrorMessage()
    {
        return Mage::helper('Magento_Catalog_Helper_Data')->__('We found a duplicate website, tier price, customer group and quantity.');
    }

    /**
     * Whether tier price value fixed or percent of original price
     *
     * @param Magento_Catalog_Model_Product_Type_Price $priceObject
     * @return bool
     */
    protected function _isPriceFixed($priceObject)
    {
        return $priceObject->isTierPriceFixed();
    }
}
