/**
 * CatalogPermissions control for admin system configuration field
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!window.Enterprise) {
    window.Enterprise = {};
}

if (!Enterprise.CatalogPermissions) {
    Enterprise.CatalogPermissions = {};
}

Enterprise.CatalogPermissions.Config = Class.create();

Object.extend(Enterprise.CatalogPermissions.Config.prototype, {
    initialize: function () {
        Event.observe(window.document, 'dom:loaded', this.handleDomLoaded.bindAsEventListener(this));
    },
    handleDomLoaded: function () {
        $$('.enterprise-grant-select').each(function(element) {
            element.observe('change', this.updateFields.bind(this));
        }, this);

        this.updateFields();
    },

    updateFields: function() {
        $$('.enterprise-grant-select').each(function(element) {
            if (parseInt(element.value) !== 2) {
                element.up('tr').next('tr').hide();
            } else {
                element.up('tr').next('tr').show();
            }

            if (element.hasClassName('browsing-catagories')) {
                if (parseInt(element.value) === 1) {
                    element.up('tr').next('tr', 1).hide();
                } else {
                    element.up('tr').next('tr', 1).show();
                }
            }
        });
     }
});

new Enterprise.CatalogPermissions.Config();