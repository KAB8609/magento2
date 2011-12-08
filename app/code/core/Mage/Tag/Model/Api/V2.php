<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product Tag API
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Api_V2 extends Mage_Tag_Model_Api
{
    /**
     * Retrieve list of tags for specified product as array of objects
     *
     * @param int $productId
     * @param string|int $store
     * @return array
     */
    public function items($productId, $store)
    {
        $result = parent::items($productId, $store);
        foreach ($result as $key => $tag) {
            $result[$key] = Mage::helper('Mage_Api_Helper_Data')->wsiArrayPacker($tag);
        }
        return $result;
    }

    /**
     * Add tag(s) to product.
     * Return array of objects
     *
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        $result = array();
        foreach (parent::add($data) as $key => $value) {
            $result[] = array('key' => $key, 'value' => $value);
        }

        return $result;
    }

    /**
     * Retrieve tag info as object
     *
     * @param int $tagId
     * @param string|int $store
     * @return object
     */
    public function info($tagId, $store)
    {
        $result = parent::info($tagId, $store);
        $result = Mage::helper('Mage_Api_Helper_Data')->wsiArrayPacker($result);
        foreach ($result->products as $key => $value) {
            $result->products[$key] = array('key' => $key, 'value' => $value);
        }
        return $result;
    }

    /**
     * Convert data from object to array before add
     *
     * @param object $data
     * @return array
     */
    protected function _prepareDataForAdd($data)
    {
        Mage::helper('Mage_Api_Helper_Data')->toArray($data);
        return parent::_prepareDataForAdd($data);
    }

    /**
     * Convert data from object to array before update
     *
     * @param object $data
     * @return array
     */
    protected function _prepareDataForUpdate($data)
    {
        Mage::helper('Mage_Api_Helper_Data')->toArray($data);
        return parent::_prepareDataForUpdate($data);
    }
}
