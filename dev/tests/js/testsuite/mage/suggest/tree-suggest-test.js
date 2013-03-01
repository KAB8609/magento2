/**
 * {license_notice}
 *
 * @category    mage.js
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */

TreeSuggestTest = TestCase('TreeSuggestTest');
TreeSuggestTest.prototype.setUp = function() {
    /*:DOC += <input name="test-suggest" id="tree-suggest" />*/
    this.suggestElement = jQuery('#tree-suggest');
};
TreeSuggestTest.prototype.tearDown = function() {
    this.treeSuggestDestroy();
};
TreeSuggestTest.prototype.treeSuggestDestroy = function() {
    if(this.suggestElement.data('treeSuggest')) {
        this.suggestElement.treeSuggest('destroy');
    }
};
TreeSuggestTest.prototype.treeSuggestCreate = function(options, element) {
    return (element || this.suggestElement).treeSuggest(options || {} ).data('treeSuggest');
};
TreeSuggestTest.prototype.uiHash = {
    item: {
        id: 1,
        label: 'Test Label'
    }
};
TreeSuggestTest.prototype.stub = function(instance, methodName, retVal) {
    var d = $.Deferred();
    if(instance && instance[methodName]) {
        instance[methodName] = function() {
            d.resolve(arguments);
            if(retVal) {
                return retVal;
            }
        }
    }
    return d.promise();
};

TreeSuggestTest.prototype.testInit = function() {
    var treeSuggestInstance = this.treeSuggestCreate();
    assertTrue(this.suggestElement.is(':mage-treeSuggest'));
    assertEquals(treeSuggestInstance.widgetEventPrefix, 'suggest');
};
TreeSuggestTest.prototype.testBind = function() {
    var event = jQuery.Event('keydown'),
        proxyEventsExecuted = false,
        treeSuggestInstance = this.treeSuggestCreate();

    treeSuggestInstance.dropdown.show();

    event.keyCode = jQuery.ui.keyCode.LEFT;
    this.stub(treeSuggestInstance, '_proxyEvents').done(function() {
        proxyEventsExecuted = true
    });

    treeSuggestInstance.element.trigger(event);
    assertTrue(proxyEventsExecuted);

    event.keyCode = $.ui.keyCode.RIGHT;
    proxyEventsExecuted = false;
    this.stub(treeSuggestInstance, '_proxyEvents').done(function() {
        proxyEventsExecuted = true
    });

    treeSuggestInstance.dropdown.show();
    treeSuggestInstance.element.trigger(event);
    assertTrue(proxyEventsExecuted);
};
TreeSuggestTest.prototype.testFilterSelected = function() {
    var treeSuggestInstance = this.treeSuggestCreate();
    assertEquals(treeSuggestInstance._filterSelected([this.uiHash.item], {_allShown: true}), [this.uiHash.item]);
};