<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Weee
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Weee_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Assign custom renderer for product create/edit form weee attribute element
     *
     * @param Varien_Event_Observer $observer
     * @return  Mage_Weee_Model_Observer
     */
    public function setWeeeRendererInForm(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form

        $form = $observer->getEvent()->getForm();
//        $product = $observer->getEvent()->getProduct();

        $attributes = Mage::getSingleton('weee/tax')->getWeeeAttributeCodes(true);
        foreach ($attributes as $code) {
            if ($weeeTax = $form->getElement($code)) {
                $weeeTax->setRenderer(
                    Mage::app()->getLayout()->createBlock('weee/renderer_weee_tax')
                );
            }
        }

        return $this;
    }

    /**
     * Exclude WEEE attributes from standard form generation
     *
     * @param Varien_Event_Observer $observer
     * @return  Mage_Weee_Model_Observer
     */
    public function updateExcludedFieldList(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_form_prepare_excluded_field_list

        $block      = $observer->getEvent()->getObject();
        $list       = $block->getFormExcludedFieldList();
        $attributes = Mage::getSingleton('weee/tax')->getWeeeAttributeCodes(true);
        $list       = array_merge($list, array_values($attributes));

        $block->setFormExcludedFieldList($list);

        return $this;
    }

    /**
     * Get empty select object
     *
     * @return Varien_Db_Select
     */
    protected function _getSelect()
    {
        return Mage::getSingleton('weee/tax')->getResource()->getReadConnection()->select();
    }

    /**
     * Add new attribute type to manage attributes interface
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Weee_Model_Observer
     */
    public function addWeeeTaxAttributeType(Varien_Event_Observer $observer)
    {
        // adminhtml_product_attribute_types

        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        $types[] = array(
            'value' => 'weee',
            'label' => Mage::helper('Mage_Weee_Helper_Data')->__('Fixed Product Tax'),
            'hide_fields' => array(
                'is_unique',
                'is_required',
                'frontend_class',
                'is_configurable',

                '_scope',
                '_default_value',
                '_front_fieldset',
            ),
            'disabled_types' => array(
                Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
            )
        );

        $response->setTypes($types);

        return $this;
    }

    /**
     * Automaticaly assign backend model to weee attributes
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Weee_Model_Observer
     */
    public function assignBackendModelToAttribute(Varien_Event_Observer $observer)
    {
        $backendModel = Mage_Weee_Model_Attribute_Backend_Weee_Tax::getBackendModelName();
        /** @var $object Mage_Eav_Model_Entity_Attribute_Abstract */
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == 'weee') {
            $object->setBackendModel($backendModel);
            if (!$object->getApplyTo()) {
                $applyTo = array();
                foreach (Mage_Catalog_Model_Product_Type::getOptions() as $option) {
                    if ($option['value'] == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
                        continue;
                    }
                    $applyTo[] = $option['value'];
                }
                $object->setApplyTo($applyTo);
            }
        }

        return $this;
    }

    /**
     * Add custom element type for attributes form
     *
     * @param   Varien_Event_Observer $observer
     */
    public function updateElementTypes(Varien_Event_Observer $observer)
    {
        $response = $observer->getEvent()->getResponse();
        $types    = $response->getTypes();
        $types['weee'] = Mage::getConfig()->getBlockClassName('weee/element_weee_tax');
        $response->setTypes($types);
        return $this;
    }

    /**
     * Update WEEE amounts discount percents
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Weee_Model_Observer
     */
    public function updateDiscountPercents(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Mage_Weee_Helper_Data')->isEnabled()) {
            return $this;
        }

        $productCondition = $observer->getEvent()->getProductCondition();
        if ($productCondition) {
            $eventProduct = $productCondition;
        } else {
            $eventProduct = $observer->getEvent()->getProduct();
        }
        Mage::getModel('weee/tax')->updateProductsDiscountPercent($eventProduct);

        return $this;
    }

    /**
     * Update configurable options of the product view page
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Weee_Model_Observer
     */
    public function updateCofigurableProductOptions(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Mage_Weee_Helper_Data')->isEnabled()) {
            return $this;
        }

        $response = $observer->getEvent()->getResponseObject();
        $options  = $response->getAdditionalOptions();

        $_product = Mage::registry('current_product');
        if (!$_product) {
            return $this;
        }
        if (!Mage::helper('Mage_Weee_Helper_Data')->typeOfDisplay($_product, array(0, 1, 4))) {
            return $this;
        }
        $amount     = Mage::helper('Mage_Weee_Helper_Data')->getAmount($_product);
        $origAmount = Mage::helper('Mage_Weee_Helper_Data')->getOriginalAmount($_product);

        $options['oldPlusDisposition'] = $origAmount;
        $options['plusDisposition'] = $amount;

        $response->setAdditionalOptions($options);

        return $this;
    }

    /**
     * Process bundle options selection for prepare view json
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Weee_Model_Observer
     */
    public function updateBundleProductOptions(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Mage_Weee_Helper_Data')->isEnabled()) {
            return $this;
        }

        $response = $observer->getEvent()->getResponseObject();
        $selection = $observer->getEvent()->getSelection();
        $options = $response->getAdditionalOptions();

        $_product = Mage::registry('current_product');
        if (!Mage::helper('Mage_Weee_Helper_Data')->typeOfDisplay($_product, array(0, 1, 4))) {
            return $this;
        }
        $typeDynamic = Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend::DYNAMIC;
        if (!$_product || $_product->getPriceType() != $typeDynamic) {
            return $this;
        }

        $amount = Mage::helper('Mage_Weee_Helper_Data')->getAmount($selection);
        $options['plusDisposition'] = $amount;

        $response->setAdditionalOptions($options);

        return $this;
    }
}

