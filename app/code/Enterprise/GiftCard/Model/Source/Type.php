<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Model_Source_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = array();
        foreach ($this->_getValues() as $k => $v) {
            $result[] = array(
                'value' => $k,
                'label' => $v,
            );
        }

        return $result;
    }

    /**
     * Get option text
     *
     * @return string|null
     */
    public function getOptionText($value)
    {
        $options = $this->_getValues();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return null;
    }

    /**
     * Get values
     *
     * @return array
     */
    protected function _getValues()
    {
        return array(
            Enterprise_GiftCard_Model_Giftcard::TYPE_VIRTUAL  => __('Virtual'),
            Enterprise_GiftCard_Model_Giftcard::TYPE_PHYSICAL => __('Physical'),
            Enterprise_GiftCard_Model_Giftcard::TYPE_COMBINED => __('Combined'),
        );
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned'  => true,
            'default'   => null,
            'extra'     => null
        );

        if (Mage::helper('Mage_Core_Helper_Data')->useDbCompatibleMode()) {
            $column['type']     = 'tinyint';
            $column['is_null']  = true;
        } else {
            $column['type']     = Varien_Db_Ddl_Table::TYPE_SMALLINT;
            $column['nullable'] = true;
            $column['comment']  = 'Enterprise Giftcard Type ' . $attributeCode . ' column';
        }

        return array($attributeCode => $column);
    }

    /**
     * Retrieve select for flat attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('Mage_Eav_Model_Resource_Entity_Attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
