/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
/*global FORM_KEY*/
/*global integration*/
(function($, window) {
    "use strict";
    $.widget('mage.integration', {
        /**
         * Options common to all instances of this widget.
         * @type {Object}
         */
        options: {
            /**
             * URL of the integration grid.
             * @type {String}
             */
            gridUrl: ''
        },

        /**
         * Bind event handler for the action when admin clicks "Save & Activate" button.
         * @private
         */
        _create: function () {
            if ($('#save-split-button-activate').length) {
                // We're on the "New integration" page - bind related handler
                this._form = $('#edit_form');
                this._form.on('saveAndActivate', $.proxy(this._saveAndActivate, this));
            }
        },

        /**
         * Save new integration, then kick off the activate dialog.
         * @private
         */
        _saveAndActivate: function () {
            if (this._form.validation && !this._form.validation('isValid')) {
                return false;
            }

            $.ajax({
                url: this._form.prop('action'),
                type: 'post',
                data: this._form.serialize(),
                dataType: 'json',
                context: this,
                beforeSend: function () {
                    $('body').trigger('processStart');
                },
                success: function (data) {
                    if (data['_redirect']) {
                        window.location.href = data['_redirect'];
                    } else if (data['integrationId']) {
                        window.integration.popup.show($('<span>').attr({
                            'data-row-dialog': 'permissions',
                            'data-row-id': data['integrationId'],
                            'data-row-name': $('#integration_properties_name').val()
                        }));
                        var that = this;
                        $('#integration-popup-container').on('dialogclose', function () {
                            $('body').trigger('processStart');
                            window.location.href = that.options.gridUrl;
                            return false;
                        });
                    }
                },
                error: function (jqXHR, status, error) {
                    alert($.mage.__('Sorry, something went wrong. Please try again later.'));
                    console && console.log(status + ': ' + error + "\nResponse text:\n" + jqXHR.responseText);
                },
                complete: function () {
                    jQuery('body').trigger('processStop');
                }
            });

            return true;
        }
    });

    window.Integration = function (permissionsDialogUrl, tokensDialogUrl, deactivateDialogUrl, reauthorizeDialogUrl) {
        var url = {
            permissions: permissionsDialogUrl,
            tokens: tokensDialogUrl,
            deactivate: deactivateDialogUrl,
            reauthorize: reauthorizeDialogUrl
        };

        var _showPopup = function (dialog, title, okButton, url) {
            $.ajax({
                url: url,
                cache: false,
                dataType: 'html',
                data: {form_key: window.FORM_KEY},
                method: 'GET',
                beforeSend: function () {
                    // Show the spinner
                    $('body').trigger('processStart');
                },
                success: function (html) {
                    var popup = $('#integration-popup-container');

                    popup.html(html);

                    var buttons = [{
                        text: $.mage.__('Cancel'),
                        click: function() {
                            $(this).dialog('close');
                        }
                    }];

                    // Add confirmation button to the list of dialog buttons
                    buttons.push(okButton);

                    popup.dialog({
                        title: title,
                        modal: true,
                        autoOpen: true,
                        minHeight: 450,
                        minWidth: 600,
                        dialogClass: 'integration-dialog',
                        position: {at: 'center'},
                        buttons: buttons
                    });
                },
                complete: function () {
                    // Hide the spinner
                    $('body').trigger('processStop');
                }
            })
        };

        return {
            popup: {
                show: function (ctx) {
                    var dialog = $(ctx).attr('data-row-dialog');

                    if (['permissions', 'deactivate', 'reauthorize', 'tokens'].indexOf(dialog) === -1) {
                        throw 'Invalid dialog type';
                    }

                    var integrationId = $(ctx).attr('data-row-id');

                    if (!integrationId) {
                        throw 'Unable to find integration ID';
                    }

                    // Replace placeholder in URL with actual ID
                    var ajaxUrl = url[dialog].replace(':id', integrationId);

                    try {
                        // Get integration name either from current element or from neighbor column
                        var integrationName = $(ctx).attr('data-row-name')
                            || $(ctx).parents('tr').find('.col-name').html().trim();
                    } catch (e) {
                        throw 'Unable to find integration name';
                    }

                    var okButton = {
                        permissions: {
                            text: $.mage.__('Allow'),
                            'class': 'primary',
                            // This data is going to be used in the next dialog
                            'data-row-id': integrationId,
                            'data-row-name': integrationName,
                            'data-row-dialog': 'tokens',
                            click: function () {
                                // Find the 'Allow' button and clone - it has all necessary data, but is going to be
                                // destroyed along with the current dialog
                                var ctx = $(this).parent().find('button.primary').clone(true);
                                $(this).dialog('destroy');
                                // Make popup out of data we saved from 'Allow' button
                                window.integration.popup.show(ctx);
                            }
                        },
                        tokens: {
                            text: $.mage.__('Activate'),
                            'class': 'primary',
                            click: function () {
                                alert('Not implemented');
                            }
                        }
                    };

                    _showPopup(dialog, integrationName, okButton[dialog], ajaxUrl);
                }
            }
        };
    };
})(jQuery, window);
