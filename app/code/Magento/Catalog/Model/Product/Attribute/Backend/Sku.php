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
 * Catalog product SKU backend attribute model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Sku extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Maximum SKU string length
     *
     * @var string
     */
    const SKU_MAX_LENGTH = 64;

    /**
     * Validate SKU
     *
     * @param Magento_Catalog_Model_Product $object
     * @throws Magento_Core_Exception
     * @return bool
     */
    public function validate($object)
    {
        $helper = Mage::helper('Magento_Core_Helper_String');
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if ($this->getAttribute()->getIsRequired() && $this->getAttribute()->isValueEmpty($value)) {
            return false;
        }

        if ($helper->strlen($object->getSku()) > self::SKU_MAX_LENGTH) {
            Mage::throwException(
                __('SKU length should be %1 characters maximum.', self::SKU_MAX_LENGTH)
            );
        }
        return true;
    }

    /**
     * Generate and set unique SKU to product
     *
     * @param $object Magento_Catalog_Model_Product
     */
    protected function _generateUniqueSku($object)
    {
        $attribute = $this->getAttribute();
        $entity = $attribute->getEntity();
        $increment = $this->_getLastSimilarAttributeValueIncrement($attribute, $object);
        $attributeValue = $object->getData($attribute->getAttributeCode());
        while (!$entity->checkAttributeUniqueValue($attribute, $object)) {
            $sku = trim($attributeValue);
            if (strlen($sku . '-' . ++$increment) > self::SKU_MAX_LENGTH) {
                $sku = substr($sku, 0, -strlen($increment) - 1);
            }
            $sku = $sku . '-' . $increment;
            $object->setData($attribute->getAttributeCode(), $sku);
        }
    }

    /**
     * Make SKU unique before save
     *
     * @param Magento_Object $object
     * @return Magento_Catalog_Model_Product_Attribute_Backend_Sku
     */
    public function beforeSave($object)
    {
        $this->_generateUniqueSku($object);
        return parent::beforeSave($object);
    }

    /**
     * Return increment needed for SKU uniqueness
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param Magento_Catalog_Model_Product $object
     * @return int
     */
    protected function _getLastSimilarAttributeValueIncrement($attribute, $object)
    {
        $adapter = $this->getAttribute()->getEntity()->getReadConnection();
        $select = $adapter->select();
        $value = $object->getData($attribute->getAttributeCode());
        $bind = array(
            'entity_type_id' => $attribute->getEntityTypeId(),
            'attribute_code' => trim($value) . '-%'
        );

        $select
            ->from($this->getTable(), $attribute->getAttributeCode())
            ->where('entity_type_id = :entity_type_id')
            ->where($attribute->getAttributeCode() . ' LIKE :attribute_code')
            ->order(array('entity_id DESC', $attribute->getAttributeCode() . ' ASC'))
            ->limit(1);
        $data = $adapter->fetchOne($select, $bind);
        return abs((int)str_replace($value, '', $data));
    }
}