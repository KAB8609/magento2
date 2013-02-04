/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

;
(function($) {
    'use strict';

    $.widget('mage.globalSearch', {
        options: {
            header: '.header',
            headerActiveClass: 'active',
            form: '#form-search',
            input: 'input',
            inputDefaultWidth: 50,
            inputOpenedWidth: 350,
            submitButton: 'button[type="submit"]',
            timeoutId: null,
            actionSpeed: 500
        },

        _create: function() {
            this.header = $(this.options.header);
            this.form = $(this.options.form);
            this.input = $(this.options.input, this.form);
            this.submitButton = $(this.options.submitButton, this.form);

            this._events();
        },

        _events: function() {
            var self = this;

            this.form
                .on('submit.submitGlobalSearchRequest', function() {
                    if (!self.input.val()) {
                        self.header.addClass(self.options.headerActiveClass);
                        self.input
                            .animate({
                                width: self.options.inputOpenedWidth
                            }, self.options.actionSpeed)
                            .focus();
                    } else {
                        this.submit();
                    }

                    return false;
                });

            this.input
                .on('blur.resetGlobalSearchForm', function() {
                    if (!self.input.val()) {
                        self.timeoutId && clearTimeout(self.timeoutId);
                        self.timeoutId = setTimeout(function() {
                            self.input
                                .animate({
                                    width: self.options.inputDefaultWidth
                                }, 200, function() {
                                    var callbackTimeout = setTimeout(function() {
                                        self.header.removeClass(self.options.headerActiveClass);
                                    }, self.options.actionSpeed);
                                });
                        }, self.options.actionSpeed);
                    }
                });

            this.submitButton
                .on('click.activateGlobalSearch', function() {
                    self.timeoutId && clearTimeout(self.timeoutId);
                });
        }
    });

    $.widget('mage.globalNavigation', {
        options: {
            menuCategory: '.level-0.parent',
            menuLinks: 'a'
        },

        _create: function() {
            this.menu = this.element;
            this.menuCategory = $(this.options.menuCategory, this.menu);
            this.menuLinks = $(this.options.menuLinks, this.menuCategory);

            this._events();
        },

        _events: function() {
            var self = this;

            var config = {
                interval: 100,
                over: self._hoverEffects, // function = onMouseOver callback (REQUIRED)
                timeout: 700, // number = milliseconds delay before onMouseOut
                out: self._leaveEffects // function = onMouseOut callback (REQUIRED)
            };

            this.menuCategory
                .hoverIntent(config)
                .on('hover', function() {
                    $(this)
                    .addClass('recent')
                    .siblings('.level-0')
                    .removeClass('recent');
/*                    $(this)
                        .siblings('.level-0')
                            .removeClass('hover')
                            .find('> .submenu')
                                .hide();*/
                });

            this.menuLinks
                .on('focus.tabFocus', function() {
                    $(this).closest('.level-0.parent')
                        .trigger('mouseenter');
                })
                .on('blur.tabFocus', function() {
                    $(this).closest('.level-0.parent')
                        .trigger('mouseleave');
                });
        },

        _hoverEffects: function () {
            var availableWidth = parseInt($(this).parent().css('width')) - $(this).position().left,
                submenu = $('> .submenu', this),
                colsWidth = 0;

            $(this)
                .addClass('hover')
/*                .siblings('.level-0.parent')
                .find('> .submenu').hide()*/
                ;

            submenu.show();

            $.each($('> .submenu > ul li.column', this), function() {
                colsWidth = colsWidth + parseInt($(this).css('width'));
            });

            var containerPaddings =  parseInt(submenu.css('padding-left')) + parseInt(submenu.css('padding-right'));

            $(this).toggleClass('reverse', (containerPaddings + colsWidth) > availableWidth);

            submenu
                .hide()
                .slideDown('fast');
        },

        _leaveEffects: function () {
            var self = $(this);

            $('> .submenu', this)
                .slideUp('fast', function() {
                    self.removeClass('hover');
                });
        }
    });

    $.widget('mage.modalPopup', {
        options: {
            popup: '.popup',
            btnClose: '[data-dismiss="popup"]'
        },

        _create: function() {
            this.fade = this.element;
            this.popup = $(this.options.popup, this.fade);
            this.btnClose = $(this.options.btnClose, this.popup);

            this._events();
        },

        _events: function() {
            var self = this;

            this.btnClose
                .on('click.closeModalPopup', function() {
                    self.fade.remove();
                });
        }
    });

    $.widget('mage.loadingPopup', {
        options: {
            message: 'Please wait...',
            timeout: 5000,
            timeoutId: null,
            callback: null,
            template: null
        },

        _create: function() {
            this.template =
                '<div class="popup popup-loading">' +
                    '<div class="popup-inner">' + this.options.message + '</div>' +
                '</div>';

            this.popup = $(this.template);

            this._show();
            this._events();
        },

        _events: function() {
            var self = this;

            this.element
                .on('showLoadingPopup', function() {
                    self._show();
                })
                .on('hideLoadingPopup', function() {
                    self._hide();
                });
        },

        _show: function() {
            var self = this;

            this.element.append(this.popup);

            if (this.options.timeout) {
                this.options.timeoutId = setTimeout(function() {
                    self._hide();

                    self.options.callback && self.options.callback();

                    self.options.timeoutId && clearTimeout(self.options.timeoutId);
                }, self.options.timeout);
            }
        },

        _hide: function() {
            this.popup.remove();
            this.destroy();
        }
    });

    $.widget('mage.useDefault', {
        options: {
            field: '.field',
            useDefault: '.use-default',
            checkbox: '.use-default-control',
            label: '.use-default-label'
        },

        _create: function() {
            this.el = this.element;
            this.field = $(this.el).closest(this.options.field);
            this.useDefault = $(this.options.useDefault, this.field);
            this.checkbox = $(this.options.checkbox, this.useDefault);
            this.label = $(this.options.label, this.useDefault);
            this.origValue = this.el.attr('data-store-label');

            this._events();
        },

        _events: function() {
            var self = this;

            this.el
                .on('change.toggleUseDefaultVisibility keyup.toggleUseDefaultVisibility', $.proxy(this._toggleUseDefaultVisibility, this))
                .trigger('change.toggleUseDefaultVisibility');

            this.checkbox
                .on('change.setOrigValue', function() {
                    if ($(this).prop('checked')) {
                        self.el
                            .val(self.origValue)
                            .trigger('change.toggleUseDefaultVisibility');

                        $(this).prop('checked', false);
                    }
                });
        },

        _toggleUseDefaultVisibility: function() {
            var curValue = this.el.val(),
                origValue = this.origValue;

            this[curValue != origValue ? '_show' : '_hide']();
        },

        _show: function() {
            this.useDefault.show();
        },

        _hide: function() {
            this.useDefault.hide();
        }
    });

    var switcherForIe8 = function() {
        /* Switcher for IE8 */
        if ($.browser.msie && $.browser.version == '8.0') {
            var checkboxSwitcher = $('.switcher input');

            var toggleCheckboxState = function(elem) {
                elem.toggleClass('checked', elem.prop('checked'));
            };
            toggleCheckboxState(checkboxSwitcher);

            $('.switcher')
                .on('change.toggleSwitcher', function() {
                    toggleCheckboxState(checkboxSwitcher);
                });
        }
    };

    $(document).ready(function() {
        $('.header-panel .search').globalSearch();
        $('.navigation').globalNavigation();
        $('.fade').modalPopup();
        $('details').details();
        $('.page-actions').floatingHeader();
        $('[data-store-label]').useDefault();

        /* Listen events on "Collapsable" events */
        $('.collapse')
            .on('show', function () {
                var fieldsetWrapper = $(this).closest('.fieldset-wrapper');

                fieldsetWrapper.addClass('opened');
            })
            .on('hide', function () {
                var fieldsetWrapper = $(this).closest('.fieldset-wrapper');

                fieldsetWrapper.removeClass('opened');
            });

        $.each($('.entry-edit'), function(i, entry) {
            $('.collapse:first', entry).collapse('show');
        });

        switcherForIe8();
    });

    $(document).on('ajaxComplete', function() {
        $('details').details();
        switcherForIe8();
    });
})(window.jQuery);