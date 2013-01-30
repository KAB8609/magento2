/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true browser:true*/
(function($) {
    'use strict';
    $.widget('mage.multisuggest', $.mage.suggest, {
        widgetEventPrefix: "suggest",
        /**
         * @override
         */
        _create: function() {
            this._super();
            this.valueField.hide();
        },

        /**
         * @override
         */
        _prepareValueField: function() {
            this._super();
            if (!this.options.valueField && this.options.selectedItems) {
                $.each(this.options.selectedItems, $.proxy(function(i, item) {
                    this._addOption(item);
                }, this));
            }
        },

        /**
         * @override
         */
        _createValueField: function() {
            return $('<select/>', {
                type: 'hidden',
                multiple: 'multiple'
            });
        },

        /**
         * @override
         */
        _selectItem: function() {
            if (this.isDropdownShown() && this._focused) {
                this._selectedItem = this._readItemData(this._focused);
                if (this.valueField.find('option[value=' + this._selectedItem.id + ']').length) {
                    this._selectedItem = this._nonSelectedItem;
                }
                if (this._selectedItem !== this._nonSelectedItem) {
                    this._term = '';
                    this._addOption(this._selectedItem);
                }
            }
        },

        /**
         * Add selected item in to select options
         * @param item
         * @private
         */
        _addOption: function(item) {
            this.valueField.append(
                '<option value="' + item.id + '" selected="selected">' + item.label + '</option>'
            );
        },

        /**
         * Remove item from select options
         * @param item
         * @private
         */
        _removeOption: function(item) {
            this.valueField.find('option[value=' + item.id + ']').remove();
        },

        /**
         * @override
         */
        _hideDropdown: function() {
            this._super();
            this.element.val('');
        }
    });

    $.widget('mage.multisuggest', $.mage.multisuggest, {
        options: {
            multiSuggestWrapper: '<ul class="category-selector-choices">' +
                '<li class="category-selector-search-field"></li></ul>',
            choiceTemplate: '<li class="category-selector-search-choice button"><div>${text}</div>' +
                '<span class="category-selector-search-choice-close" tabindex="-1" ' +
                'data-mage-init="{&quot;actionLink&quot;:{&quot;event&quot;:&quot;removeOption&quot;}}"></span></li>'
        },

        /**
         * @override
         */
        _render: function() {
            this._super();
            this.element.wrap(this.options.multiSuggestWrapper);
            this.elementWrapper = this.element.parent();
            this.valueField.find('option').each($.proxy(function(i, option) {
                option = $(option);
                this._renderOption({id: option.val(), label: option.text()});
            }, this));
        },

        /**
         * @override
         */
        _selectItem: function() {
            this._superApply(arguments);
            if (this._selectedItem !== this._nonSelectedItem) {
                this._renderOption(this._selectedItem);
            }
        },

        /**
         * Render visual element of selected item
         * @param {Object} item - selected item
         * @private
         */
        _renderOption: function(item) {
            $.tmpl(this.options.choiceTemplate, {text: item.label})
                .data(item)
                .insertBefore(this.elementWrapper)
                .trigger('contentUpdated')
                .on('removeOption', $.proxy(function(e) {
                    this._removeOption($(e.currentTarget).data());
                    $(e.currentTarget).remove();
                }, this));
        }
    });
})(jQuery);
