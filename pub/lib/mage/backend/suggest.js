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
    /**
     * Implement base functionality
     */
    $.widget('mage.suggest', {
        widgetEventPrefix: "suggest",
        options: {
            template: '',
            minLength: 1,
            /**
             * @type {(string|Array)}
             */
            source: null,
            delay: 500,
            events: {},
            appendMethod: 'after',
            controls: {
                selector: ':ui-menu, .jstree',
                eventsMap: {
                    focus: ['menufocus', 'hover_node'],
                    blur: ['menublur', 'dehover_node'],
                    select: ['menuselect', 'select_tree_node']
                }
            },
            className: null,
            inputWrapper:'<div class="mage-suggest"><div class="mage-suggest-inner"></div></div>',
            dropdownWrapper: '<div class="mage-suggest-dropdown"></div>'
        },

        /**
         * Component's constructor
         * @private
         */
        _create: function() {
            this._term = '';
            this._nonSelectedItem = {id: '', label: ''};
            this._renderedContext = null;
            this._selectedItem = this._nonSelectedItem;
            this._control = this.options.controls || {};
            this._setTemplate();
            this._prepareValueField();
            this._render();
            this._bind();
        },

        /**
         * Render base elements for suggest component
         * @private
         */
        _render: function() {
            this.dropdown = $(this.options.dropdownWrapper).hide();
            var wrapper = this.options.className ?
                $(this.options.inputWrapper).addClass(this.options.className) :
                $(this.options.inputWrapper);
            this.element
                .wrap(wrapper)
                [this.options.appendMethod](this.dropdown)
                .attr('autocomplete', 'off');
        },

        /**
         * Define a field for storing item id (find in DOM or create a new one)
         * @private
         */
        _prepareValueField: function() {
            if (this.options.valueField) {
                this.valueField = $(this.options.valueField);
            } else {
                this.valueField = this._createValueField()
                    .insertBefore(this.element)
                    .attr('name', this.element.attr('name'));
                this.element.removeAttr('name');
            }
        },

        /**
         * Create value field which keeps a id for selected option
         * can be overridden in descendants
         * @return {jQuery}
         * @private
         */
        _createValueField: function() {
            return $('<input/>', {
                type: 'hidden'
            });
        },

        /**
         * Component's destructor
         * @private
         */
        _destroy: function() {
            this.element
                .unwrap()
                .removeAttr('autocomplete');
            if (!this.options.valueField) {
                this.element.attr('name', this.valueField.attr('name'));
                this.valueField.remove();
            }
            this.dropdown.remove();
            this._off(this.element, 'keydown keyup blur');
        },

        /**
         * Return actual value of an "input"-element
         * @return {string}
         * @private
         */
        _value: function() {
            return $.trim(this.element[this.element.is(':input') ? 'val' : 'text']());
        },

        /**
         * Pass original event to a control component for handling it as it's own event
         * @param {Object} event
         * @private
         */
        _proxyEvents: function(event) {
            var fakeEvent = $.extend({}, $.Event(event.type), {
                ctrlKey: event.ctrlKey,
                keyCode: event.keyCode,
                which: event.keyCode
            });
            this.dropdown.find(this._control.selector).trigger(fakeEvent);
        },

        /**
         * Bind handlers on specific events
         * @private
         */
        _bind: function() {
            this._on($.extend({
                keydown: function(event) {
                    var keyCode = $.ui.keyCode;
                    switch (event.keyCode) {
                        case keyCode.PAGE_UP:
                        case keyCode.PAGE_DOWN:
                        case keyCode.UP:
                        case keyCode.DOWN:
                            if (!event.shiftKey) {
                                event.preventDefault();
                                this._proxyEvents(event);
                            }
                            break;
                        case keyCode.TAB:
                            if (this.isDropdownShown()) {
                                this._onSelectItem();
                                event.preventDefault();
                            }
                            break;
                        case keyCode.ENTER:
                        case keyCode.NUMPAD_ENTER:
                            if (this.isDropdownShown()) {
                                this._proxyEvents(event);
                                event.preventDefault();
                            }
                            break;
                        case keyCode.ESCAPE:
                            this._hideDropdown();
                            break;
                    }
                },
                keyup: function(event) {
                    var keyCode = $.ui.keyCode;
                    switch (event.keyCode) {
                        case keyCode.HOME:
                        case keyCode.END:
                        case keyCode.PAGE_UP:
                        case keyCode.PAGE_DOWN:
                        case keyCode.ESCAPE:
                        case keyCode.UP:
                        case keyCode.DOWN:
                        case keyCode.LEFT:
                        case keyCode.RIGHT:
                            break;
                        case keyCode.ENTER:
                        case keyCode.NUMPAD_ENTER:
                            if (this.isDropdownShown()) {
                                event.preventDefault();
                            }
                            break;
                        default:
                            this.search();
                    }
                },
                blur: this._hideDropdown,
                cut: this.search,
                paste: this.search,
                input: this.search
            }, this.options.events));

            this._bindDropdown();
        },

        /**
         * Bind handlers for dropdown element on specific events
         * @private
         */
        _bindDropdown: function() {
            var events = {
                click: function(e) {
                    // prevent default browser's behavior of changing location by anchor href
                    e.preventDefault();
                },
                mousedown: function(e) {
                    e.preventDefault();
                }
            };
            $.each(this._control.eventsMap, $.proxy(function(suggestEvent, controlEvents) {
                $.each(controlEvents, $.proxy(function(i, handlerName) {
                    switch(suggestEvent) {
                        case 'select' :
                            events[handlerName] = this._onSelectItem;
                            break;
                        case 'focus' :
                            events[handlerName] = this._focusItem;
                            break;
                        case 'blur' :
                            events[handlerName] = this._blurItem;
                            break;
                    }
                }, this));
            }, this));
            this._on(this.dropdown, events);
        },

        /**
         * Handle focus event of options item
         * @param {Object} e - event object
         * @param {Object} option
         * @private
         */
        _focusItem: function(e, option) {
            this._focused = option.item;
            this.element.val(this._readItemData(this._focused).label);
        },

        /**
         * Handle blur event of options item
         * @private
         */
        _blurItem: function() {
            this._focused = null;
            this.element.val(this._term);
        },

        /**
         *
         * @private
         */
        _onSelectItem: function() {
            this._selectItem();
            this._trigger('select', null, [this._selectedItem]);
        },

        /**
         * Save selected item and hide dropdown
         * @private
         */
        _selectItem: function() {
            if (this.isDropdownShown() && this._focused) {
                this._selectedItem = this._readItemData(this._focused);
                if (this._selectedItem !== this._nonSelectedItem) {
                    this._term = this._selectedItem.label;
                    this.valueField.val(this._selectedItem.id);
                    this._hideDropdown();
                }
            }
        },

        /**
         * Read option data from item element
         * @param {Element} item
         * @return {Object}
         * @private
         */
        _readItemData: function(item) {
            return item.data('suggestOption') || this._nonSelectedItem;
        },

        /**
         * Check if dropdown is shown
         * @return {boolean}
         */
        isDropdownShown: function() {
            return this.dropdown.is(':visible');
        },

        /**
         * Open dropdown
         * @private
         */
        _showDropdown: function() {
            if (!this.isDropdownShown()) {
                this.dropdown.show();
            }
        },

        /**
         * Close and clear dropdown content
         * @private
         */
        _hideDropdown: function() {
            this.element.val(this._selectedItem.label);
            this._renderedContext = null;
            this.dropdown.hide().empty();
        },

        /**
         * Acquire content template
         * @private
         */
        _setTemplate: function() {
            this.templateName = 'suggest' + Math.random().toString(36).substr(2);
            if ($(this.options.template).length && $(this.options.template).prop('type')=== 'text/x-jquery-tmpl') {
                $(this.options.template).template(this.templateName);
            } else {
                $.template(this.templateName, this.options.template);
            }
        },

        /**
         * Execute search process
         * @public
         */
        search: function() {
            var term = this._value();
            if (this._term !== term) {
                this._term = term;
                if (term) {
                    this._search(term);
                } else {
                    this._selectedItem = this._nonSelectedItem;
                    this.valueField.val(this._selectedItem.id);
                }
            }
        },

        /**
         * Actual search method, can be overridden in descendants
         * @param {string} term - search phrase
         * @param {Object} context - search context
         * @private
         */
        _search: function(term, context) {
            var renderer = $.proxy(function(items) {
                return this._renderDropdown(items, context || {});
            }, this);
            this.element.addClass('ui-autocomplete-loading');
            if (this.options.delay) {
                clearTimeout(this._searchTimeout);
                this._searchTimeout = this._delay(function() {
                    this._source(term, renderer);
                }, this.options.delay);
            } else {
                this._source(term, renderer);
            }
        },

        /**
         * Extend basic context with additional data (search results, search term)
         * @param {Object} context
         * @return {Object}
         * @private
         */
        _prepareDropdownContext: function(context) {
            return $.extend(context, {
                items: this._items,
                term: this._term,
                optionData: function(item) {
                    return 'data-suggest-option="' + JSON.stringify(item).replace(/"/g, '&quot;') + '"';
                }
            });
        },

        /**
         * Render content of suggest's dropdown
         * @param {Array} items - list of label+id objects
         * @param {Object} context - template's context
         * @private
         */
        _renderDropdown: function(items, context) {
            this._items = items;
            $.tmpl(this.templateName, this._prepareDropdownContext(context))
                .appendTo(this.dropdown.empty());
            this.dropdown.trigger('contentUpdated')
                .find(this._control.selector).on('focus', function(e) {
                    e.preventDefault();
                });
            this._renderedContext = context;
            this._showDropdown();
        },

        /**
         * Implement search process via specific source
         * @param {string} term - search phrase
         * @param {Function} renderer - search results handler, display search result
         * @private
         */
        _source: function(term, renderer) {
            var o = this.options;
            if ($.isArray(o.source)) {
                renderer(this.filter(o.source, term));
            } else if ($.type(o.source) === 'string') {
                if (this._xhr) {
                    this._xhr.abort();
                }
                this._xhr = $.ajax($.extend({
                    url: o.source,
                    type: 'POST',
                    dataType: 'json',
                    data: $.extend({label_part: term}, o.ajaxData || {}),
                    /* @todo refactor this to use 'response' event instead of o.response */
                    success: function(data) {
                        if ($.type(o.response) === 'function') {
                            data = o.response(data);
                        }
                        renderer(data);
                    }
                }, o.ajaxOptions || {}));
            }
        },

        _abortSearch: function() {
            clearTimeout(this._searchTimeout);
            if (this._xhr) {
                this._xhr.abort();
            }
        },

        /**
         * Perform filtering in advance loaded items and returns search result
         * @param {Array} items - all available items
         * @param {string} term - search phrase
         * @return {Object}
         */
        filter: function(items, term) {
            var matcher = new RegExp(term.replace(/[\-\/\\\^$*+?.()|\[\]{}]/g, '\\$&'), 'i');
            return $.grep(items, function(value) {
                return matcher.test(value.label || value.id || value);
            });
        }
    });

    /**
     * Implements height prediction functionality to dropdown item
     */
    /*$.widget('mage.suggest', $.mage.suggest, {
        /**
         * Extension specific options
         *//*
        options: {
            bottomMargin: 35
        },

        /**
         * @override
         * @private
         *//*
        _renderDropdown: function() {
            this._superApply(arguments);
            this._recalculateDropdownHeight();
        },

        /**
         * Recalculates height of dropdown and cut it if needed
         * @private
         *//*
        _recalculateDropdownHeight: function() {
            var dropdown = this.dropdown.css('visibility', 'hidden'),
                fromTop = dropdown.offset().top,
                winHeight = $(window).height(),
                isOverflowApplied = (fromTop + dropdown.outerHeight()) > winHeight;

            dropdown
                .css('visibility', '')
                [isOverflowApplied ? 'addClass':'removeClass']('overflow-y')
                .height(isOverflowApplied ? winHeight - fromTop - this.options.bottomMargin : '');
        }
    });*/

    /**
     * Implement storing search history and display recent searches
     */
    $.widget('mage.suggest', $.mage.suggest, {
        options: {
            showRecent: false,
            storageKey: 'suggest',
            storageLimit: 10
        },

        /**
         * @override
         * @private
         */
        _create: function() {
            if (this.options.showRecent && window.localStorage) {
                var recentItems = JSON.parse(localStorage.getItem(this.options.storageKey));
                /**
                 * @type {Array} - list of recently searched items
                 * @private
                 */
                this._recentItems = $.isArray(recentItems) ? recentItems : [];
            }
            this._super();
        },

        /**
         * @override
         * @private
         */
        _bind: function() {
            this._super();
            if (this.options.showRecent) {
                this._on({
                    focus: function() {
                        if (!this._value()) {
                            this._renderDropdown(this._recentItems);
                        }
                    }
                });
            }
        },

        /**
         * @override
         */
        search: function() {
            this._super();
            if (this.options.showRecent) {
                if (!this._term) {
                    this._abortSearch();
                    this._renderDropdown(this._recentItems);
                }
            }
        },

        /**
         * @override
         * @private
         */
        _selectItem: function() {
            this._superApply(arguments);
            if (this._selectedItem.id && this.options.showRecent) {
                this._addRecent(this._selectedItem);
            }
        },

        /**
         * Add selected item of search result into storage of recent items
         * @param {Object} item - label+id object
         * @private
         */
        _addRecent: function(item) {
            this._recentItems = $.grep(this._recentItems, function(obj){
                return obj.id !== item.id;
            });
            this._recentItems.unshift(item);
            this._recentItems = this._recentItems.slice(0, this.options.storageLimit);
            localStorage.setItem(this.options.storageKey, JSON.stringify(this._recentItems));
        }
    });

    /**
     * Implement show all functionality
     */
    $.widget('mage.suggest', $.mage.suggest, {
        /**
         * @override
         * @private
         */
        _bind: function() {
            this._super();
            this._on(this.dropdown, {
                showAll: this._showAll
            });
        },

        /**
         *
         * @private
         */
        _showAll: function() {
            this._abortSearch();
            if (!this._allItems) {
                this._search('', {_allShown: true});
            } else if (!this._renderedContext || !this._renderedContext._allShown) {
                this._renderDropdown(this._allItems, {_allShown: true});
            }
        },

        /**
         * @override
         * @param items
         * @param context
         * @private
         */
        _renderDropdown: function(items, context) {
            this._superApply(arguments);
            if (context && context._allShown && !this.allItems) {
                this._allItems = this._items;
            }
        },

        /**
         * @override
         * @private
         */
        _prepareDropdownContext: function() {
            var context = this._superApply(arguments);
            return $.extend(context, {
                allShown: function(){
                    return !!context._allShown;
                }
            });
        }
    });
})(jQuery);
