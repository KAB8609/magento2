<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Rule Combine Condition data model
 */
class Magento_CatalogRule_Model_Rule_Condition_Combine extends Magento_Rule_Model_Condition_Combine
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento_CatalogRule_Model_Rule_Condition_Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productCondition = Mage::getModel('Magento_CatalogRule_Model_Rule_Condition_Product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($productAttributes as $code => $label) {
            $attributes[] = array(
                'value' => 'Magento_CatalogRule_Model_Rule_Condition_Product|' . $code, 'label' => $label
            );
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array(
                'value' => 'Magento_CatalogRule_Model_Rule_Condition_Combine',
                'label' => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Conditions Combination')
            ),
            array(
                'label' => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Product Attribute'),
                'value' => $attributes
            ),
        ));
        return $conditions;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
