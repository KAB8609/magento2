/**
 * {license_notice}
 *
 * @category    multshipping payment
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global alert*/
(function($) {
    'use strict';
    $.widget('mage.payment', {
        options: {
            continueSelector: '#payment-continue',
            methodsContainer: '#payment-methods',
            minBalance: 0.0001

        },

        _create: function() {
            if (this.options.checkoutPrice < this.options.minBalance) {
                this._disablePaymentMethods();
            }
            this.element.find('dd [name^="payment["]').prop('disabled', true)
                .end()
                .on('click', this.options.continueSelector, $.proxy(this._submitHandler, this))
                .on('updateCheckoutPrice', $.proxy(function(event, data) {
                if (data.price) {
                    this.options.checkoutPrice += data.price;
                }
                if (data.totalPrice) {
                    data.totalPrice = this.options.checkoutPrice;
                }
                if (this.options.checkoutPrice < this.options.minBalance) {
                    // Add free input field, hide and disable unchecked checkbox payment method and all radio button payment methods
                    this._disablePaymentMethods();
                } else {
                    // Remove free input field, show all payment method
                    this._enablePaymentMethods();
                }
            }, this))
                .on('click', 'dt input:radio', $.proxy(this._paymentMethodHandler, this))
                .validation();
        },

        /**
         * Display payment details when payment method radio button is checked
         * @private
         * @param e
         */
        _paymentMethodHandler: function(e) {
            var _this = $(e.target),
                parentsDl = _this.closest('dl');
            parentsDl.find('dt input:radio').prop('checked', false);
            _this.prop('checked', true);
            parentsDl.find('dd ul').hide().find('[name^="payment["]').prop('disabled', true);
            _this.parent().nextUntil('dt').find('ul').show().find('[name^="payment["]').prop('disabled', false);
        },

        /**
         * make sure one payment method is selected
         * @private
         * @return {Boolean}
         */
        _validatePaymentMethod: function() {
            var methods = this.element.find('[name^="payment["]');
            if (methods.length === 0) {
                alert($.mage.__('Your order cannot be completed at this time as there is no payment methods available for it.'));
                return false;
            }
            if (methods.filter(':checked').length) {
                return true;
            }
            alert($.mage.__('Please specify payment method.'));
            return false;
        },

        /**
         * Disable and enable payment methods
         * @private
         */
        _disablePaymentMethods: function() {
            this.element.find('input[name="payment[method]"]').prop('disabled', true)
                .end()
                .find('input[id^="use"][name^="payment[use"]:not(:checked)').prop('disabled', true).parent().hide();
            this.element.find('[name="payment[method]"][value="free"]').parent('dt').remove();
            this.element.find(this.options.methodsContainer).hide().find('[name^="payment["]').prop('disabled', true);
            $('<input>').attr({type: 'hidden', name: 'payment[method]', value: 'free'}).appendTo(this.element);
        },

        /**
         * Enable and enable payment methods
         * @private
         */
        _enablePaymentMethods: function() {
            this.element.find('input[name="payment[method]"]').prop('disabled', false)
                .end()
                .find('input[name="payment[method]"][value="free"]').remove()
                .end()
                .find('input[id^="use"][name^="payment[use"]:not(:checked)').prop('disabled', false).parent().show();
            this.element.find(this.options.methodsContainer).show();
        },

        /**
         * Validate  before form submit
         * @private
         */
        _submitHandler: function(e) {
            e.preventDefault();
            if (this._validatePaymentMethod() &&
                this.element.validation &&
                this.element.validation('isValid')) {
                this.element.submit();
            }
        }
    });
})(jQuery);