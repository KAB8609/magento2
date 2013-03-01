<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * HTML select element block
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Block_Adminhtml_Form_Field_Stock extends Varien_Data_Form_Element_Select
{
    const QUANTITY_FIELD_HTML_ID = 'qty';

    /**
     * Quantity field element
     *
     * @var Varien_Data_Form_Element_Text
     */
    protected $_qty;

    /**
     * Is product composite (grouped or configurable)
     *
     * @var bool
     */
    protected $_isProductComposite;

    public function __construct(array $data = array())
    {
        $this->_qty = isset($data['qty']) ? $data['qty'] : $this->_createQtyElement();
        unset($data['qty']);
        parent::__construct($data);
        $this->setName($data['name']);
    }

    /**
     * Create quantity field
     *
     * @return Varien_Data_Form_Element_Text
     */
    protected function _createQtyElement()
    {
        $element = Mage::getModel('Varien_Data_Form_Element_Text');
        $element->setId(self::QUANTITY_FIELD_HTML_ID)->setName('qty');
        return $element;
    }

    /**
     * Join quantity and in stock elements' html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->_disableFields();
        return $this->_qty->getElementHtml() . parent::getElementHtml()
            . $this->_getJs(self::QUANTITY_FIELD_HTML_ID, $this->getId());
    }

    /**
     * Set form to quantity element in addition to current element
     *
     * @param $form
     * @return Varien_Data_Form
     */
    public function setForm($form)
    {
        $this->_qty->setForm($form);
        return parent::setForm($form);
    }

    /**
     * Set value to quantity element in addition to current element
     *
     * @param $value
     * @return Varien_Data_Form_Element_Select
     */
    public function setValue($value)
    {
        if (is_array($value) && isset($value['qty'])) {
            $this->_qty->setValue($value['qty']);
        }
        if (is_array($value) && isset($value['is_in_stock'])) {
            parent::setValue($value['is_in_stock']);
        }
        return $this;
    }

    /**
     * Set name to quantity element in addition to current element
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_qty->setName($name . '[qty]');
        parent::setName($name . '[is_in_stock]');
    }

    /**
     * Get whether product is configurable or grouped
     *
     * @return bool
     */
    protected function _isProductComposite()
    {
        if ($this->_isProductComposite === null) {
            $this->_isProductComposite = $this->_qty->getForm()->getDataObject()->isComposite();
        }
        return $this->_isProductComposite;
    }

    /**
     * Disable fields depending on product type
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Stock
     */
    protected function _disableFields()
    {
        if (!$this->_isProductComposite() && $this->_qty->getValue() === null) {
            $this->setDisabled('disabled');
        }
        if ($this->_isProductComposite()) {
            $this->_qty->setDisabled('disabled');
        }
        return $this;
    }

    /**
     * Get js for quantity and in stock synchronisation
     *
     * @param $quantityFieldId
     * @param $inStockFieldId
     * @return string
     */
    protected function _getJs($quantityFieldId, $inStockFieldId)
    {
        return "
            <script>
                jQuery(function($) {
                    var qty = $('#{$quantityFieldId}'),
                        productType = $('#product_type_id').val(),
                        stockAvailabilityField = $('#{$inStockFieldId}'),
                        manageStockField = $('#inventory_manage_stock'),
                        useConfigManageStockField = $('#inventory_use_config_manage_stock');

                    var disabler = function() {
                        var hasVariation = $('#config_super_product-wrapper').is('.opened');
                        if ((productType == 'configurable' && hasVariation)
                            || productType == 'grouped'
                            || productType == 'bundle'//@TODO move this check to Mage_Bundle after refactoring as widget
                            || hasVariation
                        ) {
                            return;
                        }
                        var manageStockValue = (qty.val() === '') ? 0 : 1;
                        if (manageStockValue) {
                            stockAvailabilityField.prop('disabled', false);
                        } else {
                            stockAvailabilityField.prop('disabled', true).val(0);
                        }
                        if (manageStockField.val() != manageStockValue) {
                            if (useConfigManageStockField.val() == 1) {
                                useConfigManageStockField.removeAttr('checked').val(0);
                            }
                            manageStockField.toggleClass('disabled', false).prop('disabled', false);
                            manageStockField.val(manageStockValue);
                        }
                    };

                    //Associated fields
                    var fieldsAssociations = {
                        '$quantityFieldId' : 'inventory_qty',
                        '$inStockFieldId'  : 'inventory_stock_availability'
                    };
                    //Fill corresponding field
                    var filler = function() {
                        var id = $(this).attr('id');
                        if ('undefined' !== typeof fieldsAssociations[id]) {
                            $('#' + fieldsAssociations[id]).val($(this).val());
                        } else {
                            $('#' + getKeyByValue(fieldsAssociations, id)).val($(this).val());
                        }
                    };
                    //Get key by value from object
                    var getKeyByValue = function(object, value) {
                        var returnVal = false;
                        $.each(object, function(objKey, objValue){
                            if (value === objValue) {
                                returnVal = objKey;
                            }
                        });
                        return returnVal;
                    };
                    $.each(fieldsAssociations, function(generalTabField, advancedTabField) {
                        $('#' + generalTabField + ', #' + advancedTabField)
                            .bind('focus blur change keyup click', filler)
                            .bind('keyup change blur', disabler);
                        filler.call($('#' + generalTabField));
                        filler.call($('#' + advancedTabField));
                    });
                    disabler();
                });
            </script>
        ";
    }
}
