<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_TargetRule_Model_Rule_Condition_Product_Attributes
    extends Magento_Rule_Model_Condition_Product_Abstract
{
    /**
     * Attribute property that defines whether to use it for target rules
     *
     * @var string
     */
    protected $_isUsedForRuleProperty = 'is_used_for_promo_rules';

    /**
     * Target rule codes that do not allowed to select
     * Products with status 'disabled' cannot be shown as related/cross-sells/up-sells thus rule code is useless
     *
     * @var array
     */
    protected $_disabledTargetRuleCodes = array('status');

    /**
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Eav_Model_Config $config
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Catalog_Model_Resource_Product $productResource
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection $attrSetCollection
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData,
        Magento_Rule_Model_Condition_Context $context,
        Magento_Eav_Model_Config $config,
        Magento_Catalog_Model_Product $product,
        Magento_Catalog_Model_Resource_Product $productResource,
        Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection $attrSetCollection,
        array $data = array()
    ) {
        parent::__construct(
            $backendData, $context, $config, $product, $productResource, $attrSetCollection, $data
        );
        $this->setType('Magento_TargetRule_Model_Rule_Condition_Product_Attributes');
        $this->setValue(null);
    }

    /**
     * Prepare child rules option list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            if (! in_array($code, $this->_disabledTargetRuleCodes)) {
                $conditions[] = array(
                    'value' => $this->getType() . '|' . $code,
                    'label' => $label
                );
            }
        }

        return array(
            'value' => $conditions,
            'label' => __('Product Attributes')
        );
    }
}
