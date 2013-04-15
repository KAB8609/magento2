/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    'use strict';
    $.widget("storeCreation.drawer", {
        options: {
            drawer: '#drawer',
            drawerHeader: '.drawer-header',
            drawerHeaderInner: '.drawer-header-inner',
            drawerContent: '.drawer-content',
            drawerFooter: '.drawer-footer',
            btnOpenDrawer: '.action-open-drawer',
            btnCloseDrawer: '.action-close-drawer',
            btnSaveDrawer: '.action-save-settings',
            drawerTopPosition: '.navigation',
            stickyHeaderClass: 'fixed'

        },

        _create: function() {
            this.drawerHeader = $(this.options.drawerHeader);
            this.drawerHeaderInner = $(this.options.drawerHeaderInner);
            this.drawerContent = $(this.options.drawerContent);
            this.drawerFooter = $(this.options.drawerFooter);
            this.btnOpenDrawer = $(this.options.btnOpenDrawer);
            this.btnCloseDrawer = $(this.options.btnCloseDrawer);
            this.btnSaveDrawer = $(this.options.btnSaveDrawer);
            this.drawerTopPosition = $(this.options.drawerTopPosition);
            this._startDrawerClose = false;
            this._bind();
            this._handleHash();
        },

        _bind: function() {
            $('body')
                .on('click.openDrawer', this.options.btnOpenDrawer, this._setButtonHandler);

            this.btnCloseDrawer
                .on('click.closeDrawer', $.proxy(this.drawerClose, this));

            this.btnSaveDrawer
                .on('click.saveSettings', $.proxy(function(e) {
                var elem = $(e.currentTarget),
                    drawerForm = $("#drawer-form"),
                    postData;

                if (!drawerForm.valid()) {
                    return false;
                }

                postData = drawerForm.serialize() + '&' + $.param({tileCode: elem.attr('tile-code')});
                var ajaxOptions = {
                    type: 'POST',
                    showLoader: true,
                    data: postData,
                    dataType: 'json',
                    url: elem.attr('data-save-url'),
                    success: $.proxy(this._drawerAfterSave, this),
                    error: this._ajaxFailure
                };
                $.ajax(ajaxOptions);

                return true;
            }, this));

            $(window)
                .scroll($.proxy(this._drawerFixedHeader, this))
                .hashchange($.proxy(this._handleHash, this))
                .resize($.proxy(function(){
                delay && clearTimeout(delay);
                var delay = setTimeout($.proxy(this._drawerMinHeight, this), 100);
            }, this));
        },

        _drawerMinHeight: function() {
            var bodyHeight = $('body').outerHeight(),
                windowOffsetTop = $(window).scrollTop(),
                headerHeight = this.drawerTopPosition.offset().top,
                newMinHeight = bodyHeight + windowOffsetTop - headerHeight;

            this.element.css({
                'min-height': newMinHeight
            });
        },

        /**
         * Check if page has been completed (i.e. all related tiles are complete)
         *
         * @return boolean
         */
        _isPageComplete: function() {
            var completeSteps = $('#store-launcher-content').eq(0).find('.tile-complete').length;
            return completeSteps == $('#store-launcher-content article').size();
        },

        /**
         * Show 'Launch Store' button if needed
         */
        _handleLaunchStoreButton: function() {
            var launchStoreButton = $('.action-launch-store');
            if (this._isPageComplete()) {
                launchStoreButton.prop("disabled", false);
            } else {
                launchStoreButton.prop("disabled", true);
            }
        },

        _drawerFixedHeader: function() {
            var windowOffsetTop = $(window).scrollTop(),
                headerHeight = this.drawerTopPosition.offset().top;

            if (windowOffsetTop >=  headerHeight && this.drawerHeader.is(":visible")) {
                this.element.addClass(this.options.stickyHeaderClass);
            } else {
                this.element.removeClass(this.options.stickyHeaderClass);
            }
        },

        drawerOpen: function(tileCode) {
            var elem = this.element,
                headerHeight = this.drawerTopPosition.offset().top,
                bodyHeight = $('body').outerHeight() + 500;

            this._drawerMinHeight();
            window.scrollTo(0, 0);
            this._startDrawerClose = false;

            elem
                .css('top', bodyHeight)
                .show()
                .animate({
                    top: headerHeight
                }, 1000, 'easeOutExpo', $.proxy(function() {
                this._drawerFixedHeader();
                this.drawerFooter.animate({
                    bottom: 0
                }, 100);
            }, this));
        },

        drawerClose: function() {
            if (this._startDrawerClose) {
                return;
            }
            this._startDrawerClose = true;

            window.location.hash = '';

            var elem = this.element,
                drawerFooter = this.drawerFooter,
                drawerFooterHeight = drawerFooter.height(),
                bodyHeight = $('body').outerHeight(),
                drawerSwitcher = this.drawerHeaderInner.find('.drawer-switcher');

            var hideDrawer = function() {
                elem.hide()
                    .trigger('drawerHidden');

                drawerSwitcher ? drawerSwitcher.remove() : '';
            };

            drawerFooter.animate({
                bottom: -drawerFooterHeight - 10
            }, 100);

            if (elem.hasClass(this.options.stickyHeaderClass)) {
                window.scrollTo(0, 0);
                elem.css({
                    top: 0
                });
                elem.removeClass(this.options.stickyHeaderClass)
                    .animate({
                        top: bodyHeight
                    }, 1000, hideDrawer);
            } else {
                var deltaTop = $(window).scrollTop();
                elem.animate({
                    top: deltaTop + bodyHeight
                }, 1000, hideDrawer);
            }
        },

        _drawerAfterLoad: function(result, status) {
            if (result.success) {
                $('.title', this.drawerHeader).text(result.tile_header);
                this.drawerOpen(result.tile_code);
                $('.drawer-content-inner', this.drawerContent).html(result.tile_content);
                var drawerSwitcher = $('.drawer-content-inner').find('.drawer-switcher');
                if (drawerSwitcher.length) {
                    var drawerSwitcherCopy = drawerSwitcher.clone(),
                        drawerSwitcherCheckbox = drawerSwitcherCopy.find(':checkbox'),
                        drawerSwitcherLabel = drawerSwitcherCopy.find('.switcher-label'),
                        drawerSwitcherId = drawerSwitcherCheckbox.prop('id').replace('', 'copy-');

                    drawerSwitcherLabel.prop('for', drawerSwitcherId);
                    drawerSwitcherCheckbox.prop('id', drawerSwitcherId);
                    this.drawerHeaderInner.append(drawerSwitcherCopy);
                }
            } else if (result && result.error_message) {
                alert(result.error_message);
            }
        },

        _drawerAfterSave: function(result, status) {
            if (result.success) {
                //Complete class should be added by condition for specified tile
                $('#tile-' + result.tile_code).before(result.tile_content).remove();
                this._handleLaunchStoreButton();
                window.location.hash = '';
                this.drawerClose();
            } else if (result && result.error_message) {
                alert(result.error_message);
            }
        },

        _setButtonHandler: function() {
            var hashString = $(this).closest('.tile-store-settings').attr('id');
            window.location.hash = hashString.replace('tile-', '');
        },

        _handleHash: function() {
            if (window.location.hash == '') {
                this.drawerClose();
                return;
            }
            var hashString = window.location.hash.replace('#', ''),
                elem = $('#tile-' + hashString).find(this.options.btnOpenDrawer),
                tileCode = elem.attr('data-drawer').replace('open-drawer-', ''),
                postData = {
                    tileCode: tileCode
                },
                ajaxOptions = {
                    type: 'POST',
                    showLoader: true,
                    data: postData,
                    dataType: 'json',
                    url: elem.attr('data-load-url'),
                    success: $.proxy(this._drawerAfterLoad, this),
                    error: this._ajaxFailure
                };

            $.ajax(ajaxOptions);

            this.btnSaveDrawer
                .attr('tile-code', tileCode)
                .attr('data-save-url', elem.attr('data-save-url'));
        },

        _ajaxFailure: function() {
            window.location.reload();
        }
    });

    $.widget('storeCreation.partiallyValidateDrawer', {
        options: {
            currentSection: ''
        },

        _create: function() {
            this.currentSection = $(this.options.currentSection);

            this.element.on('click.saveMethod', $.proxy(function(event) {
                event.preventDefault();
                var elem = this.element,
                    fieldset = elem.closest('.fieldset'),
                    fields = fieldset.find(':input'),
                    legend = fieldset.find('.legend'),
                    id = fieldset.attr('id'),
                    removeValidationMessage = function() {
                        setTimeout(function() {
                            $('.message-validation').slideUp(400);
                        }, 3000);
                    },
                    insertValidationMessage = function(messageText, messageType) {
                        $('<div class="message-validation ' + messageType + '">' + messageText + '</div>')
                            .insertAfter(legend);
                        removeValidationMessage();
                    };

                var validationNotPassed = fields.filter(function(index, element) {
                    return !$.validator.validateElement(element);
                }).length;

                if (validationNotPassed != 0) {
                    return;
                }

                var ajaxOptions = {
                    type: 'POST',
                    showLoader: true,
                    data: fields.serialize(),
                    dataType: 'json',
                    url: elem.attr('data-save-url'),
                    success: $.proxy(function(result, status) {
                        if (result.success) {
                            if (result && result.message) {
                                insertValidationMessage(result.message,'success');
                                this.currentSection.find('.active').addClass('configured');
                            }
                        } else {
                            if (result && result.error_message) {
                                insertValidationMessage(result.error_message,'error');
                            }
                        }
                    }, this),
                    error: function() {
                        window.location.reload();
                    }
                };
                $.ajax(ajaxOptions);
            }, this));
        }
    });

    $.widget('storeCreation.toggleStatus', {
        options: {
            formsToDisable: [],
            disabledMessage: '',
            needValidate: true,
            drawerForm: '#drawer-form'
        },

        _create: function() {
            this.drawerForm = $(this.options.drawerForm);
            this.disabledMessage = $(this.options.disabledMessage);
            this.element.on('change.drawerStatus', $.proxy(this._toggleStatus, this));
            this._toggleStatus();
        },

        _toggleStatus: function() {
            var elem = this.element,
                elemId = elem.prop('id').replace('copy-','');

            if (elem.is(':checked')) {
                $.each(this.options.formsToDisable, $.proxy(function(index, element) {
                    $(element).removeClass('disabled');
                }, this));
                if (this.options.needValidate) {
                    this.drawerForm.removeClass('ignore-validate');
                }
                this.disabledMessage.addClass('hidden');
                $('#' + elemId).prop('checked', true);
            } else {
                $.each(this.options.formsToDisable, $.proxy(function(index, element) {
                    $(element).addClass('disabled');
                }, this));
                if (this.options.needValidate) {
                    this.drawerForm.addClass('ignore-validate');
                }
                this.disabledMessage.removeClass('hidden');
                $('#' + elemId).prop('checked', false);
            }
        }
    });
})(window.jQuery);
