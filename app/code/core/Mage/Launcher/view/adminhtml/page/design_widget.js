/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    $.widget("storeCreation.drawerPages", {
        options: {
            drawer: '#drawer',
            drawerHeader: '.drawer-header',
            drawerHeaderActions: '.actions',
            btnCloseDrawer: '.action-close-drawer',
            btnSaveDrawer: '.action-save-settings'
        },

        headerButtons: {},

        drawerPages: {},
        drawerPageCurrent: null,

        _create: function() {
            this.drawerHeader = $(this.options.drawerHeader);
            this.drawerHeaderActions = this.drawerHeader.find(this.options.drawerHeaderActions);
            this.btnCloseDrawer = $(this.options.btnCloseDrawer);
            this.btnSaveDrawer = $(this.options.btnSaveDrawer);
            this.headerButtonCreate({
                name: 'close',
                button: this.btnCloseDrawer,
                click: $.proxy(this.destroy, this)
            });
            this.headerButtonCreate({
                name: 'save',
                button: this.btnSaveDrawer,
                click: $.proxy(this.destroy, this)
            });
        },

        headerButtonCreate: function(buttonOptions) {
            var buttonObject;
            if (this.headerButtons[buttonOptions.name]) {
                buttonObject = this.headerButtons[buttonOptions.name];
                buttonObject.off('.headerButton');
            }
            var buttonVars = {
                type: 'button',
                cssClass: 'primary',
                title: 'Button',
                click: function() {},
                afterShow: function() {}
            };
            $.extend(buttonVars, buttonOptions);

            if (buttonOptions.button) {
                buttonObject = buttonOptions.button;
            }

            if (!buttonObject) {
                buttonObject = $.tmpl('<button type="${type}" class="${cssClass}">${title}</button>', buttonVars);
            }

            this.drawerHeaderActions.prepend(buttonObject);
            buttonObject.on('click.headerButton', buttonVars.click);
            buttonObject.on('afterShow.headerButton', buttonVars.afterShow);

            this.headerButtons[buttonOptions.name] = buttonObject;
        },

        headerButtonHide: function(buttonName) {
            this.headerButtons[buttonName].addClass('hidden');
        },

        headerButtonHideAll: function() {
            $.each(this.headerButtons, $.proxy(function(buttonName) {
                this.headerButtonHide(buttonName);
            }, this));
        },

        headerButtonShow: function(buttonName) {
            this.headerButtons[buttonName]
                .removeClass('hidden')
                .trigger('afterShow.headerButton');
        },

        drawerPageAdd: function(page) {
            if (this.drawerPages[page.name]) {
                this.drawerPages[page.name].page.remove();
                delete  this.drawerPages[page.name];
            }
            var pageElement = {
                page: '',
                buttons: 'close'
            };
            $.extend(pageElement, page);
            pageElement.page = $(pageElement.page);
            this.drawerPages[page.name] = pageElement;
            this.drawerPageHide(page.name);
        },

        drawerPageShow: function(name) {
            if (!name || !this.drawerPages[name]) {
                return;
            }
            this.drawerPages[name].page.removeClass('hidden');
            this.headerButtonHideAll();
            $.each(this.drawerPages[name].buttons, $.proxy(function(i, buttonName) {
                this.headerButtonShow(buttonName);
            }, this));
            this.drawerPageCurrent = name;
        },

        drawerPageHide: function(name) {
            if (name && this.drawerPages[name]) {
                this.drawerPages[name].page.addClass('hidden');
            }
        },

        drawerPageSwitch: function(name) {
            this.drawerPageHide(this.drawerPageCurrent);
            this.drawerPageShow(name);
        }
    });

})(window.jQuery);
