<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * HTML select element block
 */
namespace Magento\CatalogInventory\Block\Adminhtml\Form\Field;

class Stock extends \Magento\Data\Form\Element\Select
{
    const QUANTITY_FIELD_HTML_ID = 'qty';

    /**
     * Quantity field element
     *
     * @var \Magento\Data\Form\Element\Text
     */
    protected $_qty;

    /**
     * Is product composite (grouped or configurable)
     *
     * @var bool
     */
    protected $_isProductComposite;

    /**
     * Text element factory
     *
     * @var \Magento\Data\Form\Element\TextFactory
     */
    protected $_factoryText;

    /**
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Escaper $escaper
     * @param \Magento\Data\Form\Element\TextFactory $factoryText
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Escaper $escaper,
        \Magento\Data\Form\Element\TextFactory $factoryText,
        array $data = array()
    ) {
        $this->_factoryText = $factoryText;
        $this->_qty = isset($attributes['qty']) ? $attributes['qty'] : $this->_createQtyElement();
        unset($attributes['qty']);
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setName($attributes['name']);
    }

    /**
     * Create quantity field
     *
     * @return \Magento\Data\Form\Element\Text
     */
    protected function _createQtyElement()
    {
        /** @var \Magento\Data\Form\Element\Text $element */
        $element = $this->_factoryText->create();
        $element->setId(self::QUANTITY_FIELD_HTML_ID)->setName('qty')->addClass('validate-number input-text');
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
     * @return \Magento\Data\Form
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
     * @return \Magento\Data\Form\Element\Select
     */
    public function setValue($value)
    {
        if (is_array($value) && isset($value['qty'])) {
            $this->_qty->setValue($value['qty']);
        }
        parent::setValue(is_array($value) && isset($value['is_in_stock']) ? $value['is_in_stock'] : $value);
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
     * @return \Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock
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
        // @codingStandardsIgnoreStart
        return "
            <script>
                jQuery(function($) {
                    var qty = $('#{$quantityFieldId}'),
                        productType = $('#product_type_id').val(),
                        stockAvailabilityField = $('#{$inStockFieldId}'),
                        manageStockField = $('#inventory_manage_stock'),
                        useConfigManageStockField = $('#inventory_use_config_manage_stock');

                    var disabler = function(event) {
                        var hasVariation = $('[data-panel=product-variations]').is('.opened');
                        if ((productType == 'configurable' && hasVariation)
                            || productType == 'grouped'
                            || productType == 'bundle'//@TODO move this check to Magento_Bundle after refactoring as widget
                            || hasVariation
                        ) {
                            return;
                        }
                        var manageStockValue = (qty.val() === '') ? 0 : 1;
                        stockAvailabilityField.prop('disabled', !manageStockValue);
                        if (manageStockField.val() != manageStockValue && !(event && event.type == 'keyup')) {
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

                        if ($('#inventory_manage_stock').length) {
                            fireEvent($('#inventory_manage_stock').get(0), 'change');
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
        // @codingStandardsIgnoreEnd
    }
}
