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
 * Source model for 'msrp_enabled' product attribute
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type;

class Enabled
    extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Enable MAP
     */
    const MSRP_ENABLE_YES = 1;

    /**
     * Disable MAP
     */
    const MSRP_ENABLE_NO = 0;

    /**
     * Get value from the store configuration settings
     */
    const MSRP_ENABLE_USE_CONFIG = 2;

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
                    'label' => __('Yes'),
                    'value' => self::MSRP_ENABLE_YES
                ),
                array(
                    'label' => __('No'),
                    'value' => self::MSRP_ENABLE_NO
                ),
                array(
                    'label' => __('Use config'),
                    'value' => self::MSRP_ENABLE_USE_CONFIG
                )
            );
        }
        return $this->_options;
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
            'unsigned'  => false,
            'default'   => null,
            'extra'     => null
        );

        if (\Mage::helper('Magento\Core\Helper\Data')->useDbCompatibleMode()) {
            $column['type']     = 'tinyint(1)';
            $column['is_null']  = true;
        } else {
            $column['type']     = \Magento\DB\Ddl\Table::TYPE_SMALLINT;
            $column['length']   = 1;
            $column['nullable'] = true;
            $column['comment']  = $attributeCode . ' column';
        }

        return array($attributeCode => $column);
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return \Magento\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return \Mage::getResourceModel('Magento\Eav\Model\Resource\Entity\Attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
