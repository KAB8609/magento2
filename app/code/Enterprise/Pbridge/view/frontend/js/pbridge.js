/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.pbridge', {
        options : {
            method: '',
            frameUrl: '',
            iframeContainerSelector: '[data-container="iframe"]',
            bodySelector: '[data-container="body"]',
            pmtsBtnsContainerSelector: '#payment-buttons-container',
            reloadSelector: '.pbridge-reload'
        },

        _create : function() {
            this.element
                .on('reloadPbridgeIframe', $.proxy(function(event, data) { this.reloadIframe(data); }, this))
                .find('span.pbridge-reload a').on('click', $.proxy(function () {
                    var data = {};
                    data.method = this.options.method;
                    data.frameUrl = this.options.frameUrl;
                    this._reloadIframe(data);

                    return false;
                }, this));
            this.element.closest('[data-container="body"]').on('reloadIframe', $.proxy(this._reloadIframe,this));
        },

        /**
         *
         * @param data
         */
        _reloadIframe: function(data) {
            if (!data) {
                data = {
                    method: this.options.method,
                    frameUrl: this.options.frameUrl
                };
            }

            var hiddenElms = this.element.find('input:hidden');
            if (hiddenElms.length > 0) {
                hiddenElms.remove();
            }

            $.ajax({
                url: data.frameUrl,
                type: 'post',
                context: this,
                data: {method_code: data.method},
                success: function(response) {
                    this.element.find(this.options.iframeContainerSelector).html(response);
                    this.element.trigger('gotoSection', 'payment').trigger('contentUpdate');
                    this._toggleContinueButton(this.element.parents('ol'));
                    this.element.find(this.options.reloadSelector).find('a').show();
                }
            });
        },

        /**
         *
         * @param target
         * @return {*}
         * @private
         */
        _toggleContinueButton: function(target) {
            var buttonsContainer = $(this.options.pmtsBtnsContainerSelector);
            if (buttonsContainer.length > 0 && buttonsContainer.find('button.button')) {
                var continueButton = buttonsContainer.find('button.button');
                if (target.type !== 'checkbox' || ! target.checked) {
                    var checkedRadio = $('#co-payment-form input[type="radio"][name="payment[method]"]:checked');
                    if (checkedRadio.length) {
                        var iframeContainer = $('#payment_form_' + checkedRadio[0].value + '_container');
                        // check whether it is Bridge payment method
                        if (iframeContainer.length > 0 && iframeContainer.prevAll('span.pbridge-reload').length > 0) {
                            return continueButton.hide();
                        }
                    }
                }
                continueButton.show();
            }
        }

        /**
         *
         */
        /*
         preLoadReviewIframe: function() {
         if (review.agreementsForm) {
         checkout.setLoadWaiting('review');
         var params = Form.serialize(payment.form) + '&' + Form.serialize(review.agreementsForm);
         var request = new Ajax.Request(
         _getAgreementValidationUrl(),
         {
         method: 'post',
         parameters: params,
         onComplete: review.onComplete,
         onSuccess: _validateAgreements,
         onFailure: checkout.ajaxFailure.bind(checkout)
         }
         );
         } else {
         _loadReviewIframe();
         }
         },
         */

        /**
         *
         * @param method
         * @private
         */
        /*
         _loadReviewIframe: function(method) {
         var iframeContainer = $('review_iframe_container');
         var methodCode = method || payment.currentMethod;
         new Ajax.Updater(
         iframeContainer,
         "<?php echo $this->getUrl('enterprise_pbridge/pbridge/review', array('_current' => true, '_secure' => true)); ?>",
         {parameters : {method_code : methodCode, data: {is_review_iframe: 1}}}
         );
         $('iframe-warning').show();
         $('btn-pay-now').hide();
         },
         */

        /**
         *
         * @param transport
         * @private
         */
        /*
         _validateAgreements: function(transport) {
         if (transport && transport.responseText) {
         try{
         response = eval('(' + transport.responseText + ')');
         }
         catch (e) {
         response = {};
         }
         if (response.success) {
         // Accepted terms and conditions are no longer available to decline
         review.agreementsForm.hide();
         loadReviewIframe();
         } else {
         review.nextStep(transport);
         }
         }
         },
         */

        /**
         *
         * @return {String}
         * @private
         */
        /*
         _getAgreementValidationUrl: function() {
         return "<?php echo $this->getUrl('enterprise_pbridge/pbridge/validateAgreement', array('_current' => true)); ?>";
         }
         */
    });
})(jQuery);
