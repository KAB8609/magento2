/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    /**
     * Widget panel
     */
    $.widget('mage.sortable', $.ui.sortable, {
        options: {
            moveUpEvent:   'moveUp',
            moveDownEvent: 'moveDown'
        },

        _create: function() {
            this._super();
            this.initButtons();
            this.bind();
        },

        initButtons: function() {
            this.element.find('input.up').on('click', $.proxy(function(event){
                $('body').trigger(this.options.moveUpEvent, {item:$(event.target).parent('li')});
            }, this));
            this.element.find('input.down').on('click', $.proxy(function(event){
                $('body').trigger(this.options.moveDownEvent, {item:$(event.target).parent('li')});
            }, this));
        },

        bind: function() {
            var $body = $('body');
            $body.on(this.options.moveUpEvent, $.proxy(this._onMoveUp, this));
            $body.on(this.options.moveDownEvent, $.proxy(this._onMoveDown, this));
        },

        _onMoveUp: function(event, data) {
            data.item.insertBefore(data.item.prev());
        },

        _onMoveDown: function(event, data) {
            data.item.insertAfter(data.item.next());
        }
    });

})(jQuery);