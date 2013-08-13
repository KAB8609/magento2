<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Weee_Model_Observer extends Magento_Core_Model_Abstract
{
    /**
     * Assign custom renderer for product create/edit form weee attribute element
     *
     * @param Magento_Event_Observer $observer
     * @return  Magento_Weee_Model_Observer
     */
    public function setWeeeRendererInForm(Magento_Event_Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form

        $form = $observer->getEvent()->getForm();
//        $product = $observer->getEvent()->getProduct();

        $attributes = Mage::getSingleton('Magento_Weee_Model_Tax')->getWeeeAttributeCodes(true);
        foreach ($attributes as $code) {
            if ($weeeTax = $form->getElement($code)) {
                $weeeTax->setRenderer(
                    Mage::app()->getLayout()->createBlock('Magento_Weee_Block_Renderer_Weee_Tax')
                );
            }
        }

        return $this;
    }

    /**
     * Exclude WEEE attributes from standard form generation
     *
     * @param Magento_Event_Observer $observer
     * @return  Magento_Weee_Model_Observer
     */
    public function updateExcludedFieldList(Magento_Event_Observer $observer)
    {
        //adminhtml_catalog_product_form_prepare_excluded_field_list

        $block      = $observer->getEvent()->getObject();
        $list       = $block->getFormExcludedFieldList();
        $attributes = Mage::getSingleton('Magento_Weee_Model_Tax')->getWeeeAttributeCodes(true);
        $list       = array_merge($list, array_values($attributes));

        $block->setFormExcludedFieldList($list);

        return $this;
    }

    /**
     * Get empty select object
     *
     * @return Magento_DB_Select
     */
    protected function _getSelect()
    {
        return Mage::getSingleton('Magento_Weee_Model_Tax')->getResource()->getReadConnection()->select();
    }

    /**
     * Add new attribute type to manage attributes interface
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Weee_Model_Observer
     */
    public function addWeeeTaxAttributeType(Magento_Event_Observer $observer)
    {
        // adminhtml_product_attribute_types

        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        $types[] = array(
            'value' => 'weee',
            'label' => Mage::helper('Magento_Weee_Helper_Data')->__('Fixed Product Tax'),
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
                Magento_Catalog_Model_Product_Type::TYPE_GROUPED,
            )
        );

        $response->setTypes($types);

        return $this;
    }

    /**
     * Automaticaly assign backend model to weee attributes
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Weee_Model_Observer
     */
    public function assignBackendModelToAttribute(Magento_Event_Observer $observer)
    {
        $backendModel = Magento_Weee_Model_Attribute_Backend_Weee_Tax::getBackendModelName();
        /** @var $object Magento_Eav_Model_Entity_Attribute_Abstract */
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == 'weee') {
            $object->setBackendModel($backendModel);
            if (!$object->getApplyTo()) {
                $applyTo = array();
                foreach (Magento_Catalog_Model_Product_Type::getOptions() as $option) {
                    if ($option['value'] == Magento_Catalog_Model_Product_Type::TYPE_GROUPED) {
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
     * @param   Magento_Event_Observer $observer
     */
    public function updateElementTypes(Magento_Event_Observer $observer)
    {
        $response = $observer->getEvent()->getResponse();
        $types    = $response->getTypes();
        $types['weee'] = 'Magento_Weee_Block_Element_Weee_Tax';
        $response->setTypes($types);
        return $this;
    }

    /**
     * Update WEEE amounts discount percents
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Weee_Model_Observer
     */
    public function updateDiscountPercents(Magento_Event_Observer $observer)
    {
        if (!Mage::helper('Magento_Weee_Helper_Data')->isEnabled()) {
            return $this;
        }

        $productCondition = $observer->getEvent()->getProductCondition();
        if ($productCondition) {
            $eventProduct = $productCondition;
        } else {
            $eventProduct = $observer->getEvent()->getProduct();
        }
        Mage::getModel('Magento_Weee_Model_Tax')->updateProductsDiscountPercent($eventProduct);

        return $this;
    }

    /**
     * Update configurable options of the product view page
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Weee_Model_Observer
     */
    public function updateConfigurableProductOptions(Magento_Event_Observer $observer)
    {
        /* @var $helper Magento_Weee_Helper_Data */
        $helper = Mage::helper('Magento_Weee_Helper_Data');
        if (!$helper->isEnabled()) {
            return $this;
        }

        $response = $observer->getEvent()->getResponseObject();
        $options  = $response->getAdditionalOptions();

        $_product = Mage::registry('current_product');
        if (!$_product) {
            return $this;
        }

        $options['oldPlusDisposition'] = $helper->getOriginalAmount($_product);
        $options['plusDisposition'] = $helper->getAmount($_product);

        // Exclude Weee amount from excluding tax amount
        if (!$helper->typeOfDisplay($_product, array(
            Magento_Weee_Model_Tax::DISPLAY_INCL, Magento_Weee_Model_Tax::DISPLAY_INCL_DESCR,
        ))) {
            $options['exclDisposition'] = true;
        }

        $response->setAdditionalOptions($options);

        return $this;
    }

    /**
     * Process bundle options selection for prepare view json
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Weee_Model_Observer
     */
    public function updateBundleProductOptions(Magento_Event_Observer $observer)
    {
        /* @var $weeeHelper Magento_Weee_Helper_Data */
        $weeeHelper = Mage::helper('Magento_Weee_Helper_Data');
        if (!$weeeHelper->isEnabled()) {
            return $this;
        }

        $response = $observer->getEvent()->getResponseObject();
        $selection = $observer->getEvent()->getSelection();
        $options = $response->getAdditionalOptions();

        $_product = Mage::registry('current_product');

        $typeDynamic = Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend::DYNAMIC;
        if (!$_product || $_product->getPriceType() != $typeDynamic) {
            return $this;
        }

        $amount          = $weeeHelper->getAmount($selection);
        $attributes      = $weeeHelper->getProductWeeeAttributes($_product, null, null, null, $weeeHelper->isTaxable());
        $amountInclTaxes = $weeeHelper->getAmountInclTaxes($attributes);
        $taxes           = $amountInclTaxes - $amount;
        $options['plusDisposition']    = $amount;
        $options['plusDispositionTax'] = ($taxes < 0) ? 0 : $taxes;
        // Exclude Weee amount from excluding tax amount
        if (!$weeeHelper->typeOfDisplay($_product, array(0, 1, 4))) {
            $options['exclDisposition'] = true;
        }

        $response->setAdditionalOptions($options);

        return $this;
    }
}
