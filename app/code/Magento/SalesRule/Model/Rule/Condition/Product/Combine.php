<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_SalesRule_Model_Rule_Condition_Product_Combine extends Magento_Rule_Model_Condition_Combine
{
    /**
     * @var Magento_SalesRule_Model_Rule_Condition_Product
     */
    protected $_ruleConditionProd;

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_SalesRule_Model_Rule_Condition_Product $ruleConditionProduct
     * @param array $data
     */
    public function __construct(
        Magento_Rule_Model_Condition_Context $context,
        Magento_SalesRule_Model_Rule_Condition_Product $ruleConditionProduct,
        array $data = array())
    {
        parent::__construct($context, $data);
        $this->_ruleConditionProd = $ruleConditionProduct;
        $this->setType('Magento_SalesRule_Model_Rule_Condition_Product_Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_ruleConditionProd->loadAttributeOptions()->getAttributeOption();
        $pAttributes = array();
        $iAttributes = array();
        foreach ($productAttributes as $code=>$label) {
            if (strpos($code, 'quote_item_') === 0) {
                $iAttributes[] = array(
                    'value' => 'Magento_SalesRule_Model_Rule_Condition_Product|' . $code, 'label' => $label
                );
            } else {
                $pAttributes[] =
                    array('value' => 'Magento_SalesRule_Model_Rule_Condition_Product|' . $code, 'label' => $label);
            }
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'Magento_SalesRule_Model_Rule_Condition_Product_Combine',
                'label' => __('Conditions Combination')
            ),
            array('label' => __('Cart Item Attribute'),
                'value' => $iAttributes
            ),
            array('label' => __('Product Attribute'),
                'value' => $pAttributes
            ),
        ));
        return $conditions;
    }

    /**
     * @param $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
