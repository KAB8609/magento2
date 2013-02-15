/**
 * {license_notice}
 *
 * @category    checkout multi-shipping review order overview
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
/*global alert:true*/
(function($) {
    "use strict";
    $.widget('mage.overview', {
        options: {
            opacity: 0.5, // CSS opacity for the 'Place Order' button when it's clicked and then disabled.
            pleaseWaitLoader: '#review-please-wait', // 'Submitting order information...' Ajax loader.
            placeOrderSubmit: '#review-buttons-container button[type="submit"]', // The 'Place Order' button.
            agreements: '#checkout-agreements' // Container for all of the checkout billing agreements.
        },

        /**
         * Bind a submit handler to the form.
         * @private
         */
        _create: function() {
            this.element.on('submit', $.proxy(this._showLoader, this));
        },

        /**
         * Verify that all agreements and terms/conditions are checked. Show the Ajax loader. Disable
         * the submit button (i.e. Place Order).
         * @return {Boolean}
         * @private
         */
        _showLoader: function() {
            if ($(this.options.agreements).find('input[type="checkbox"]:not(:checked)').length > 0) {
                alert($.mage.__('Please agree to all Terms and Conditions before placing the orders.'));
                return false;
            }
            $(this.options.pleaseWaitLoader).show();
            $(this.options.placeOrderSubmit).prop('disabled', true).css('opacity', this.options.opacity);
            return true;
        }
    });
})(jQuery);
