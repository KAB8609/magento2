/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {
    /**
     * Class for managing skin selector control
     */
    DesignEditorSkinSelector = function (config) {
        this._init(config);
        this._addListener();
        return this;
    }

    DesignEditorSkinSelector.prototype._init = function (config) {
        this._skinControlSelector = '#' + config.selectId;
        this._backParams = config.backParams;
        this.changeSkinUrl = config.changeSkinUrl;
        return this;
    }

    DesignEditorSkinSelector.prototype._addListener = function () {
        var thisObj = this;
        $(this._skinControlSelector).change(
            function () {thisObj.changeSkin()}
        );
        return this;
    }

    DesignEditorSkinSelector.prototype.changeSkin = function () {
        var separator = /\?/.test(this.changeSkinUrl) ? '&' : '?';

        var params = {skin: $(this._skinControlSelector).val()};
        for (var i in this._backParams) {
            params[i] = this._backParams[i];
        }

        var url = this.changeSkinUrl + separator + $.param(params);

        window.location.href = url;
        return this;
    }

    /**
     * Class for design editor
     */
    DesignEditor = function () {
        this._init();
    }

    DesignEditor.prototype._init = function () {
        this._dragged = null;
        this._placeholder = null;

        this._templateWrapper = '<div class="vde_block_wrapper" />';
        this._templateBlockTitle = '<div class="vde_block_title">%BLOCK_NAME%</div>';
        this._templatePlaceholder = '<div class="vde_placeholder"></div>';

        this._wrapBlocks()
            ._enableDragging();
        return this;
    }

    DesignEditor.prototype._wrapBlocks = function () {
        var thisObj = this;
        $('.vde_marker[marker_type=start]')
            .filter(function (index) {
                return $(this).parent().css('display') == 'block';
            })
            .each(function (index) {
                var marker = $(this);
                var titleHtml = thisObj._templateBlockTitle.replace('%BLOCK_NAME%', marker.attr('block_name'));
                $(titleHtml).insertAfter(marker);
                marker.nextUntil('.vde_marker[marker_type=end]').wrapAll(thisObj._templateWrapper);
            });
        $('.vde_marker').remove();
        return this;
    }

    DesignEditor.prototype._enableDragging = function () {
        var thisObj = this;
        $('.vde_block_wrapper').draggable({
            helper: 'clone',
            revert: true,
            start: function (event, ui) {thisObj._onDragStarted(event, ui)},
            stop: function (event, ui) {thisObj._onDragStopped(event, ui)}
        });
        return this;
    }

    DesignEditor.prototype._onDragStarted = function (event, ui) {
        if (this._dragged) {
            return this;
        }
        this._hideDragged($(event.target))
            ._putPlaceholder();
    }

    DesignEditor.prototype._onDragStopped = function (event, ui) {
        if (!this._dragged) {
            return this;
        }
        this._removePlaceholder()
            ._showDragged();
    }

    DesignEditor.prototype._hideDragged = function (dragged) {
        this._showDragged(); // Maybe some other dragged element was hidden before, just retore it
        this._dragged = dragged;
        this._dragged.css('visibility', 'hidden');
        return this;
    }

    DesignEditor.prototype._showDragged = function () {
        if (!this._dragged) {
            return this;
        }
        this._dragged.css('visibility', 'visible');
        this._dragged = null;
        return this;
    }

    DesignEditor.prototype._putPlaceholder = function () {
        if (!this._placeholder) {
            this._placeholder = $(this._templatePlaceholder);
        }
        this._placeholder.css('height', this._dragged.outerHeight() + 'px')
            .css('width', this._dragged.outerWidth() + 'px');
        this._placeholder.insertBefore(this._dragged);
        return this;
    }

    DesignEditor.prototype._removePlaceholder = function () {
        if (!this._placeholder) {
            return this;
        }
        this._placeholder.remove();
        return this;
    }
})(jQuery);
