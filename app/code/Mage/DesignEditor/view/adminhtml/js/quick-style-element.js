/**
 * {license_notice}
 *
 * @category    design
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    'use strict';
    $.widget('vde.quickStyleElement', {
        options: {
            changeEvent: 'change.quickStyleElement',
            focusEvent: 'focus.quickStyleElement',
            saveQuickStylesUrl: null
        },

        _init: function() {
            this._bind();
        },

        _bind: function() {
            this.element.on(this.options.changeEvent, $.proxy(this._onChange, this));
            this.element.on(this.options.focusEvent, $.proxy(this._onFocus, this));
        },

        _onFocus: function() {
            this.oldValue = $(this.element).val();
        },

        _onChange: function() {
            if (this.element.attr('type') == 'checkbox') {
                this.element.trigger('quickStyleElementBeforeChange');
            }

            if (this.oldValue != $(this.element).val() || this.element.attr('type') == 'checkbox') {
                this._send()
            }
        },

        _send: function() {
            var data = {
                id: this.element.attr('id'),
                value: this.element.val()
            };

            $.ajax({
                type: 'POST',
                url:  this.options.saveQuickStylesUrl,
                data: data,
                dataType: 'json',
                success: $.proxy(function(response) {
                    if (response.error) {
                        alert(response.message);
                    }
                    this.element.trigger('refreshIframe');
                }, this),
                error: function() {
                    alert($.mage.__('Error: unknown error.'));
                }
            });
        }
    });
})(window.jQuery);
