/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {

    $.widget('vde.storeSelector', {
        options: {
            assignEvent:        'assign',
            assignSaveEvent:    'assign-save',
            loadEvent:          'loaded',
            storeView: {
                windowSelector: '#store-view-window'
            },
            closePopupBtn: '[class^="action-close"]',
            assignSaveUrl: null,
            afterAssignSaveUrl: null,
            storesByThemes: {},
            isMultipleStoreViewMode: null,
            redirectToVdeOnAssign: false
        },

        /**
         * Identifier of a theme currently processed
         *
         * It is set in showStoreViews(), used and then cleared in _onAssignSave()
         */
        themeId: null,

        /**
         * Form creation
         * @protected
         */
        _create: function() {
            this._bind();
        },

        /**
         * Bind handlers
         * @protected
         */
        _bind: function() {
            //this.element is <body>
            this.element.on(this.options.assignEvent, $.proxy(this._onAssign, this));
            this.element.on(this.options.assignSaveEvent, $.proxy(this._onAssignSave, this));
            this.element.on('click.closePopup', this.options.closePopupBtn, $.proxy(this._closePopup, this));
            this.element.on('keyup', $.proxy(function(e) {
                //ESC button
                if (e.keyCode === 27) {
                    this._closePopup();
                }
            }, this));
            this.element.on(this.options.loadEvent, $.proxy(function() {
                this.element.trigger('contentUpdated');
            }, this));
        },

        _closePopup: function(event, data) {
            $(this.options.storeView.windowSelector).hide();
            this.themeId = null;
        },

        /**
         * Assign event handler
         * @protected
         */
        _onAssign: function(event, data) {
            if (this.options.isMultipleStoreViewMode) {
                this.showStoreViews(data.theme_id);
            } else {
                if (!this._confirm($.mage.__(
                    'You are about to change this theme for your live store, are you sure want to do this?\n\n' +
                    'Note: only virtual theme can be assigned, if you are assigning physical theme then ' +
                    'virtual copy will be created automatically and assigned instead.'
                ))) {
                    return;
                }
                this.assignSaveTheme(data.theme_id, null);
            }
        },

        /**
         * "Assign Save" button click handler (button is on "Select Store-views" popup)
         * @protected
         */
        _onAssignSave: function() {
            var stores = [];
            var checkedValue = 1;
            $(this.options.storeView.windowSelector).find('form').serializeArray().each(function(object, index) {
                if (parseInt(object.value, 10) === checkedValue) {
                    stores.push(parseInt(object.name.match('storeviews\\[(\\d+)\\]')[1], 10));
                }
            });

            if (!this._isStoreChanged(this.themeId, stores)) {
                alert($.mage.__('No stores were reassigned.'));
                return;
            }

            var popUp = $(this.options.storeView.windowSelector);
            popUp.hide();

            this.assignSaveTheme(this.themeId, stores);
            this.themeId = null;
        },

        /**
         * Check if the stores changed
         * @protected
         */
        _isStoreChanged: function(themeId, storesToAssign) {
            var assignedStores = this.options.storesByThemes[themeId] || [] ;
            return !(storesToAssign.length === assignedStores.length &&
                $(storesToAssign).not(assignedStores).length === 0);
        },

        /**
         * Assign event handlers
         * @protected
         */
        _confirm: function(message) {
            return confirm(message);
        },

        /**
         * Show store-view selector window
         * @public
         */
        showStoreViews: function(themeId) {
            var popUp = $(this.options.storeView.windowSelector);
            var storesByThemes = this.options.storesByThemes;
            popUp.find('input[type=checkbox]').each(function(index, element) {
                element = $(element);

                var storeViewId = parseInt(element.attr('id').replace('storeview_', ''), 10);
                element.attr('checked',
                    !(!storesByThemes[themeId] || storesByThemes[themeId].indexOf(storeViewId) === -1));

            });
            this.themeId = themeId;
            popUp.show();
        },

        /**
         * Send AJAX request to assign theme to store-views
         * @public
         */
        assignSaveTheme: function(themeId, stores) {
            if (!this.options.assignSaveUrl) {
                throw Error($.mage.__('Url to assign themes to store is not defined'));
            }

            var data = {
                theme_id: themeId,
                stores:   stores
            };
            //TODO since we can't convert data to JSON string we use magic numbers
            var DEFAULT_STORE = '-1';
            var EMPTY_STORES = '-2';
            if (data.stores === null) {
                data.stores = DEFAULT_STORE;
            } else if (data.stores.length === 0) {
                data.stores = EMPTY_STORES;
            }

            $('#messages').html('');
            $.ajax({
                type: 'POST',
                url:  this.options.assignSaveUrl,
                data: data,
                dataType: 'json',
                success: $.proxy(function(response) {
                    this.assignSaveThemeSuccess(response, stores, themeId);
                }, this),
                error: function() {
                    alert($.mage.__('Error: unknown error.'));
                }
            });
        },

        /**
         * Assign Save Theme AJAX call Success handler
         *
         * @param response
         * @param stores
         * @param themeId
         */
        assignSaveThemeSuccess: function(response, stores, themeId) {
            if (response.error) {
                alert($.mage.__('Error') + ': "' + response.message + '".');
            } else if (this.options.redirectToVdeOnAssign) {
                var defaultStore = 0;
                var url = [
                    this.options.afterAssignSaveUrl + 'store_id',
                    stores ? stores[0] : defaultStore,
                    'theme_id',
                    response.themeId
                ].join('/');
                this.options.storesByThemes[themeId] = stores;

                document.location = url;
            }
        },

        /**
         * Prepare items for post request
         *
         * @param items
         * @return {Object}
         * @private
         */
        _preparePostItems: function(items) {
            var postData = {};
            $.each(items, function(index, item){
                postData[index] = item.getPostData();
            });
            return postData;
        }
    });

})(jQuery);
