<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_TargetRule_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Set condition type
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_TargetRule_Model_Rule_Condition_Combine');
    }

    /**
     * Prepare list of contitions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array(
                'value' => $this->getType(),
                'label' => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Conditions Combination')
            ),
            Mage::getModel('Enterprise_TargetRule_Model_Rule_Condition_Product_Attributes')->getNewChildSelectOptions(),
        );

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * Collect validated attributes for Product Collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     * @return Enterprise_TargetRule_Model_Rule_Condition_Combine
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
