<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_SalesRule_Model_Rule_Action_Product extends Mage_Rule_Model_Action_Abstract
{
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'rule_price'=>Mage::helper('Mage_SalesRule_Helper_Data')->__('Special Price'),
        ));
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            'to_fixed'=>Mage::helper('Mage_SalesRule_Helper_Data')->__('To Fixed Value'),
            'to_percent'=>Mage::helper('Mage_SalesRule_Helper_Data')->__('To Percentage'),
            'by_fixed'=>Mage::helper('Mage_SalesRule_Helper_Data')->__('By Fixed value'),
            'by_percent'=>Mage::helper('Mage_SalesRule_Helper_Data')->__('By Percentage'),
        ));
        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().Mage::helper('Mage_SalesRule_Helper_Data')->__("Update product's %1 %2: %3", $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml());
        $html.= $this->getRemoveLinkHtml();
        return $html;
    }
}
