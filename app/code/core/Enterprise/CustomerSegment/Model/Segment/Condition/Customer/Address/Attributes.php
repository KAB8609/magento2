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
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


class Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Address_Attributes
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_customer_address_attributes');
        $this->setValue(null);
    }

    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            $conditions[] = array('value'=> $this->getType() . '|' . $code, 'label'=>$label);
        }

        return array('value' => $conditions, 'label'=>Mage::helper('enterprise_customersegment')->__('Address Attributes'));
    }

    /**
     * Load attribute options
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Product
     */
    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('customer/address')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            if ($attribute->getIsUsedForCustomerSegment()) {
                $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Retrieve select option values
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = Mage::getModel('adminhtml/system_config_source_country')
                        ->toOptionArray();
                    break;

                case 'region_id':
                    $options = Mage::getModel('adminhtml/system_config_source_allregion')
                        ->toOptionArray();
                    break;

                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Retrieve attribute element
     *
     * @return Varien_Form_Element_Abstract
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }


    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'country_id': case 'region_id':
                return 'select';
        }
        return 'string';
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'country_id': case 'region_id':
                return 'select';
        }
        return 'text';
    }

    public function asHtml()
    {
        return Mage::helper('enterprise_customersegment')->__('Customer Address %s', parent::asHtml());
    }

    /**
     * Retrieve attribute object
     *
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttributeObject()
    {
        try {
            $obj = Mage::getSingleton('eav/config')
                ->getAttribute('customer_address', $this->getAttribute());
        }
        catch (Exception $e) {
            $obj = new Varien_Object();
            $obj->setEntity(Mage::getResourceSingleton('customer/customer_address'))
                ->setFrontendInput('text');
        }
        return $obj;
    }

    public function getConditionsSql($customer, $isRoot = false)
    {
        $attribute = $this->getAttributeObject();

        $table = $attribute->getBackendTable();

        $operator = $this->_getSqlOperator();

        $select = $this->getResource()->createSelect();
        $select->from($table, array(new Zend_Db_Expr(1)))
            ->limit(1);
        $select->where($this->_createCustomerFilter($customer, 'main.entity_id', $isRoot));

        if ($attribute->getBackendType() == 'static') {
            $select->where("{$attribute->getAttributeCode()} {$operator} ?", $this->getValue());
        } else {
            $select->where('attribute_id = ?', $attribute->getId())
                ->where("value {$operator} ?", $this->getValue());
        }

        return $select;
    }
}
