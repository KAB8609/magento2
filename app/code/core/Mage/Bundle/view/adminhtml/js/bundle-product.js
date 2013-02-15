/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global FORM_KEY*/
/*global bSelection*/
(function($) {
    $.widget('mage.bundleProduct', {
        _create: function () {
            this._initOptionBoxes();
            this._initSortableSelections();
            this._bindCheckboxHandlers();
            this._bindAddSelectionDialog();
            this._hideProductTypeSwitcher();
            this._bindPanelVisibilityToggler();
        },
        _initOptionBoxes: function () {
            this.element.sortable({
                axis: 'y',
                handle: '.draggable-handle',
                items: '.option-box',
                update: this._updateOptionBoxPositions,
                tolerance: 'pointer'
            });

            var syncOptionTitle = function (event) {
                $(event.target).closest('.option-box').find('.head-edit-form').text($(event.target).val());
            };
            this._on({
                'change .option-box input[name$="[title]"]': syncOptionTitle,
                'keyup .option-box input[name$="[title]"]': syncOptionTitle
            });
        },
        _initSortableSelections: function () {
            this.element.find('.option-box .form-list tbody').sortable({
                axis: 'y',
                handle: '.draggable-handle',
                helper: function(event, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                update: this._updateSelectionsPositions,
                tolerance: 'pointer'
            });
        },
        _bindAddSelectionDialog: function () {
            var widget = this;
            this._on({'click .add-selection': function (event) {
                var $optionBox = $(event.target).closest('.option-box'),
                    $selectionGrid = $optionBox.find('.selection-search'),
                    optionIndex = $optionBox.attr('id').replace('bundle_option_', ''),
                    productIds = [],
                    productSkus = [];

                $optionBox.find('[name$="[product_id]"]').each(function () {
                    if (!$(this).closest('tr').find('[name$="[delete]"]').val()) {
                        productIds.push($(this).val());
                        productSkus.push($(this).closest('tr').find('.product-sku').text());
                    }
                });

                bSelection.gridSelection.set(optionIndex, $H({}));
                bSelection.gridRemoval = $H({});
                bSelection.gridSelectedProductSkus = productSkus;
                $selectionGrid.dialog({
                    title: $optionBox.find('input[name$="[title]"]').val() === '' ?
                        'Add Products to New Option' :
                        'Add Products to Option "' +
                            $('<div>').text($optionBox.find('input[name$="[title]"]').val()).html() + '"',
                    autoOpen: false,
                    minWidth: 980,
                    modal: true,
                    resizable: true,
                    buttons: [{
                        text: 'Cancel',
                        click: function() {
                            $selectionGrid.dialog('close');
                        }
                    }, {
                        text: 'Apply Changes',
                        'class': 'add',
                        click: function() {
                            console.log('click');
                            bSelection.gridSelection.get(optionIndex).each(
                                function(pair) {
                                    bSelection.addRow(optionIndex, {
                                        name: pair.value.get('name'),
                                        selection_price_value: 0,
                                        selection_qty: 1,
                                        sku: pair.value.get('sku'),
                                        product_id: pair.key,
                                        option_id: $('bundle_selection_id_' + optionIndex).val()
                                    });
                                }
                            );
                            bSelection.gridRemoval.each(
                                function(pair) {
                                    $optionBox.find('.product-sku').filter(function () {
                                        return $.trim($(this).text()) == pair.key; // find row by SKU
                                    }).closest('tr').find('button.delete').trigger('click');
                                }
                            );
                            widget.refreshSortableElements();
                            widget._updateSelectionsPositions.apply(widget.element);
                            $selectionGrid.dialog('close');
                        }
                    }],
                    close: function() {
                        $(this).dialog('destroy');
                    }
                });

                $.ajax({
                    url: bSelection.selectionSearchUrl,
                    dataType: 'html',
                    data: {
                        index: optionIndex,
                        products: productIds,
                        selected_products: productIds,
                        form_key: FORM_KEY
                    },
                    success: function(data) {
                        $selectionGrid.html(data).dialog('open');
                    },
                    context: $('body'),
                    showLoader: true
                });
            }});
        },
        _hideProductTypeSwitcher: function () {
            $('#weight_and_type_switcher, label[for=weight_and_type_switcher]').hide();
        },
        _bindPanelVisibilityToggler: function () {
            var element = this.element;
            this._on('#product_info_tabs', {
                tabsbeforeactivate: function (event, ui) {
                    element[$(ui.newPanel).find('#attribute-name-container').length ? 'show' : 'hide']();
                }
            });
        },
        _bindCheckboxHandlers: function () {
            this._on({
                'change .is-required': function (event) {
                    var $this = $(event.target);
                    $this.closest('.option-box').find('[name$="[required]"]').val($this.is(':checked') ? 1 : 0);
                },
                'change .is-user-defined-qty': function (event) {
                    var $this = $(event.target);
                    $this.closest('.qty-box').find('.select').val($this.is(':checked') ? 1 : 0);
                }
            });
            this.element.find('.is-required').each(function () {
                $(this).prop('checked', $(this).closest('.option-box').find('[name$="[required]"]').val() > 0);
            });
            this.element.find('.is-user-defined-qty').each(function () {
                $(this).prop('checked', $(this).closest('.qty-box').find('.select').val() > 0);
            });
        },
        _updateOptionBoxPositions: function () {
            $(this).find('[name^=bundle_options][name$="[position]"]').each(function (index) {
                $(this).val(index);
            });
        },
        _updateSelectionsPositions: function () {
            $(this).find('[name^=bundle_selections][name$="[position]"]').each(function (index) {
                $(this).val(index);
            });
        },
        refreshSortableElements: function () {
            this.element.sortable('refresh');
            this._updateOptionBoxPositions.apply(this.element);
            this._initSortableSelections();
            return this;
        }
    });
})(jQuery);
