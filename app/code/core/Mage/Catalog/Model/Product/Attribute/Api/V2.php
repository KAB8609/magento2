<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product attribute api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Api_V2 extends Mage_Catalog_Model_Product_Attribute_Api
{
    /**
     * Create new product attribute
     *
     * @param array $data input data
     * @return integer
     */
    public function create($data)
    {
        $helper = Mage::helper('Mage_Api_Helper_Data');
        $helper->v2AssociativeArrayUnpacker($data);
        Mage::helper('Mage_Api_Helper_Data')->toArray($data);
        return parent::create($data);
    }

    /**
     * Update product attribute
     *
     * @param string|integer $attribute attribute code or ID
     * @param array $data
     * @return boolean
     */
    public function update($attribute, $data)
    {
        $helper = Mage::helper('Mage_Api_Helper_Data');
        $helper->v2AssociativeArrayUnpacker($data);
        Mage::helper('Mage_Api_Helper_Data')->toArray($data);
        return parent::update($attribute, $data);
    }

    /**
     * Add option to select or multiselect attribute
     *
     * @param  integer|string $attribute attribute ID or code
     * @param  array $data
     * @return bool
     */
    public function addOption($attribute, $data)
    {
        Mage::helper('Mage_Api_Helper_Data')->toArray($data);
        return parent::addOption($attribute, $data);
    }

    /**
     * Get full information about attribute with list of options
     *
     * @param integer|string $attribute attribute ID or code
     * @return array
     */
    public function info($attribute)
    {
        $result = parent::info($attribute);
        if (!empty($result['additional_fields'])){
            $keys = array_keys($result['additional_fields']);
            foreach ($keys as $key ) {
                $result['additional_fields'][] = array(
                    'key' => $key,
                    'value' => $result['additional_fields'][$key]
                );
                unset($result['additional_fields'][$key]);
            }
        }
        return $result;
    }
}
