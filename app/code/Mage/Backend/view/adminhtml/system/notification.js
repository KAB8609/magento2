/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function ($) {
    $.template(
        'systemMessageDialog',
        '<li class="{{if severity == 1}}warning{{else}}warning{{/if}}">{{html text}}{{if link}} <a href="${link}">Go there now</a>{{/if}}</li>'
    );

    $.widget('mage.systemMessageDialog', $.ui.dialog, {
        options: {
            systemMessageTemplate: 'systemMessageDialog'
        },
        open: function(severity) {
            var superMethod = $.proxy(this._super, this);
            $.ajax({
                url: this.options.ajaxUrl,
                type: 'GET',
                data: {severity: severity}
            }).done($.proxy(function(data) {
                this.element.html(($('<ul />', {class: "message-system-list"}).append($.tmpl(this.options.systemMessageTemplate, data)))).trigger('contentUpdated');
                superMethod();
            }, this));
            return this;
        }
    });

    $(document).ready(function(){
        $('body').on('surveyYes surveyNo', function(e, data) {
            if (e.type == 'surveyYes') {
                var win = window.open(data.surveyUrl, '', 'width=900,height=600,resizable=1,scrollbars=1');
                win.focus();
            }
            $.ajax({
                url: data.surveyAction,
                type: 'post',
                data: {decision: data.decision}
            })
        });

        $('#system_messages .message-system-short .error').on('click', function(e) {
            $('#message-system-all').systemMessageDialog('open', 1);
        });
        $('#system_messages .message-system-short .warning').on('click', function() {
            $('#message-system-all').systemMessageDialog('open', 2);
        });
    });
})(jQuery);

