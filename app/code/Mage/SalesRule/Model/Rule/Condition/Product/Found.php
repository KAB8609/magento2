<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_SalesRule_Model_Rule_Condition_Product_Found
    extends Mage_SalesRule_Model_Rule_Condition_Product_Combine
{
    /**
     * @param Mage_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Mage_SalesRule_Model_Rule_Condition_Product_Found');
    }

    /**
     * Load value options
     *
     * @return Mage_SalesRule_Model_Rule_Condition_Product_Found
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            1 => Mage::helper('Mage_SalesRule_Helper_Data')->__('FOUND'),
            0 => Mage::helper('Mage_SalesRule_Helper_Data')->__('NOT FOUND')
        ));
        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml()
            . Mage::helper('Mage_SalesRule_Helper_Data')->__("If an item is %1 in the cart with %2 of these conditions true:", $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * validate
     *
     * @param Varien_Object $object Quote
     * @return boolean
     */
    public function validate(Varien_Object $object)
    {
        $all = $this->getAggregator() === 'all';
        $true = (bool)$this->getValue();
        $found = false;
        foreach ($object->getAllItems() as $item) {
            $found = $all;
            foreach ($this->getConditions() as $cond) {
                $validated = $cond->validate($item);
                if (($all && !$validated) || (!$all && $validated)) {
                    $found = $validated;
                    break;
                }
            }
            if (($found && $true) || (!$true && $found)) {
                break;
            }
        }
        // found an item and we're looking for existing one
        if ($found && $true) {
            return true;
        } elseif (!$found && !$true) { // not found and we're making sure it doesn't exist
            return true;
        }
        return false;
    }
}
