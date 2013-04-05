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

    $.widget('vde.toolsPanel', {
        options: {
            openedPanelClass: 'opened',
            activeTabClass: 'active',
            panelDefaultHeight: 300,
            panelMinHeight: 100,
            panelMaxHeight: 700,
            showHidePanelAnimationSpeed: 300,
            resizableArea: '.vde-tools-content',
            resizableAreaInner: '.vde-tab-content.active .vde-tab-content-inner',
            panelHeader: '.vde-tab-content-header',
            panelTab: 'a[data-toggle="tab"]',
            resizeHandlerControl: '.ui-resizable-handle',
            resizeHandlerControlContainer: '.vde-tools-handler-container',
            scrollExistClass: 'hasScroll',
            mainTabs: '.vde-tools-footer .vde-tab-controls > .item',
            btnClose: '.vde-tools-header .action-close',
            btnCloseMsg: '.vde-message .action-close'
        },

        _create: function() {
            this.panel = this.element;

            this.resizableArea = $(this.options.resizableArea);
            this.resizableAreaInner = $(this.options.resizableAreaInner);
            this.panelTab = $(this.options.panelTab);
            this.resizeHandlerControlContainer = $(this.options.resizeHandlerControlContainer);
            this.panelHeaderHeight = $(this.options.panelHeader).height();
            this.btnClose = $(this.options.btnClose, this.panel);
            this.mainTabs = $(this.options.mainTabs);
            this.btnCloseMsg = $(this.options.btnCloseMsg, this.panel);

            this._events();
        },

        _init: function() {
            $(this.options.resizeHandlerControl).prependTo(this.resizeHandlerControlContainer);
            this._recalcDataHeight(this._getResizableAreaHeight());
        },

        _events: function() {
            var self = this;

            this.resizableArea.resizable({
                handles: 'n',
                minHeight: self.options.panelMinHeight,
                maxHeight: self.options.panelMaxHeight,
                iframeFix: true,
                resize: function(event, ui) {
                    self._recalcDataHeight(ui.size.height);
                },
                stop: function(event, ui) {
                    if (ui.size.height <= self.options.panelMinHeight) {
                        self._hide();
                    }
                }
            }).bind('resize.vdeToolsResize', function () {
                self._recalcDataHeight(self._getResizableAreaHeight());
                $(this).css('top', 'auto');
            });

            this.panelTab.on('shown', function () {
                if (!self.panel.hasClass(self.options.openedPanelClass)) {
                    self._show();
                } else {
                    self._recalcDataHeight(self.options.panelDefaultHeight);
                }
                self.resizableArea.trigger('resize.vdeToolsResize');
            });

            this.btnClose.live('click.hideVDEToolsPanel', $.proxy(this._hide, this));

            this.btnCloseMsg.live('click.hideVDEMessage', $.proxy(function(e) {
                $(e.target).parents('.vde-message')[0].remove();
            }, this));
        },

        _toggleClassIfScrollBarExist: function(elem) {
            elem.toggleClass(this.options.scrollExistClass, elem.height() < $('.vde-tab-data', elem).height() + $('.vde-tools-header').height() );
        },

        _getActiveResizableAreaInner: function() {
            return $(this.options.resizableAreaInner);
        },

        _getResizableAreaHeight: function() {
            return this.resizableArea.height();
        },

        _recalcDataHeight: function(height) {
            var elem = this._getActiveResizableAreaInner();

            elem.height(height - this.panelHeaderHeight);
            this._toggleClassIfScrollBarExist(elem);
        },

        _show: function() {
            this.panel.addClass(this.options.openedPanelClass);
            this.resizableArea.animate({
                height: this.options.panelDefaultHeight - this.panelHeaderHeight
            }, this.options.showHidePanelAnimationSpeed, $.proxy(function() {
                this.resizableArea.trigger('resize.vdeToolsResize');
            }, this));
        },

        _hide: function() {
            this.panel.removeClass(this.options.openedPanelClass);

            this.resizableArea.animate({
                height: 0
            }, this.options.showHidePanelAnimationSpeed, $.proxy(function() {
                this.mainTabs.removeClass(this.options.activeTabClass);
            }, this));
        }
    });
})(window.jQuery);
