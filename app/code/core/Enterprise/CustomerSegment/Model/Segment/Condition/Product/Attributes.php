<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


class Enterprise_CustomerSegment_Model_Segment_Condition_Product_Attributes extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    protected $_isUsedForRuleProperty = 'is_used_for_customer_segment';

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_product_attributes');
        $this->setValue(null);
    }

    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            $conditions[] = array('value'=> $this->getType() . '|' . $code, 'label'=>$label);
        }

        return array('value' => $conditions, 'label' => Mage::helper('enterprise_customersegment')->__('Product Attributes'));
    }

    public function asHtml()
    {
        return Mage::helper('enterprise_customersegment')->__('Product %s', parent::asHtml());
    }

    public function getAttributeObject()
    {
        return Mage::getSingleton('eav/config')->getAttribute('catalog_product', $this->getAttribute());
    }

    public function getResource()
    {
        return Mage::getResourceSingleton('enterprise_customersegment/segment');
    }

    public function getSubfilterType()
    {
        return 'product';
    }

    public function getSubfilterSql($fieldName, $requireValid, $website)
    {
        $attribute = $this->getAttributeObject();
        $table = $attribute->getBackendTable();

        $select = $this->getResource()->createSelect();
        $select->from(array('main'=>$table), array('entity_id'));

        if ($attribute->isStatic()) {
            $condition = $this->getResource()->createConditionSql(
                "main.{$attribute->getAttributeCode()}", $this->getOperator(), $this->getValue()
            );
        } else {
            $select->where('main.attribute_id = ?', $attribute->getId());
            $storeIds = $website->getStoreIds();
            $storeIds = array_merge(array(0), $storeIds);
            $select->where('main.store_id IN (?)', $storeIds);
            $condition = $this->getResource()->createConditionSql(
                'main.value', $this->getOperator(), $this->getValue()
            );
        }
        $select->where($condition);

        $inOperator = ($requireValid ? 'IN' : 'NOT IN');

        return sprintf("%s %s (%s)", $fieldName, $inOperator, $select);
    }
}
