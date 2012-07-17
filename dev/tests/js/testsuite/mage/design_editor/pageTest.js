/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
PageTest = TestCase('PageTest');
PageTest.prototype.testInit = function() {
    var page = jQuery('body').vde_page();
    assertEquals(true, page.is(':vde-vde_page'));
    page.vde_page('destroy');
};
PageTest.prototype.testDefaultOptions = function() {
    var page = jQuery('body').vde_page();
    assertEquals('.vde_element_wrapper.vde_container', page.vde_page('option', 'containerSelector'));
    assertEquals('#vde_toolbar', page.vde_page('option', 'panelSelector'));
    assertEquals('.vde_element_wrapper', page.vde_page('option', 'highlightElementSelector'));
    assertEquals('.vde_element_title', page.vde_page('option', 'highlightElementTitleSelector'));
    assertEquals('#vde_highlighting', page.vde_page('option', 'highlightCheckboxSelector'));
    assertEquals('vde_highlighting', page.vde_page('option', 'cookieHighlightingName'));
    page.vde_page('destroy');
};
PageTest.prototype.testInitContainers = function() {
    /*:DOC += <div class="vde_element_wrapper vde_container"></div> */
    var page = jQuery('body').vde_page();
    var containerSelector = page.vde_page('option', 'containerSelector');
    assertEquals(true, jQuery(containerSelector).is(':vde-vde_container'));
    page.vde_page('destroy');
}
PageTest.prototype.testInitPanel = function() {
    /*:DOC += <div id="vde_toolbar"></div> */
    var page = jQuery('body').vde_page();
    var panelSelector = page.vde_page('option', 'panelSelector');
    assertEquals(true, jQuery(panelSelector).is(':vde-vde_panel'));
    page.vde_page('destroy');
}
PageTest.prototype.testInitHighlighting = function() {
    /*:DOC += <div id="vde_toolbar"><div id="vde_highlighting"></div></div> */
    var page = jQuery('body').vde_page();
    var highlightCheckboxSelector = page.vde_page('option', 'highlightCheckboxSelector');
    assertEquals(true, jQuery(highlightCheckboxSelector).is(':vde-vde_checkbox'));
    page.vde_page('destroy');
}
PageTest.prototype.testProcessMarkers = function() {
    /*:DOC +=
    <div>
        <div id="vde_element_1" class="vde_element_wrapper vde_container vde_wrapper_hidden">
            <div class="vde_element_title">Title 1</div>
        </div>
        <!--start_vde_element_1-->
        <div id="vde_element_2" class="vde_element_wrapper vde_draggable vde_wrapper_hidden">
            <div class="vde_element_title">Title 2</div>
        </div>
        <!--start_vde_element_2-->
        <div class="block block-list">
            <div class="block-title">
                <strong><span>Block Title</span></strong>
            </div>
            <div class="block-content">
                <p class="empty">Block Content</p>
            </div>
        </div>
        <!--end_vde_element_2-->
        <!--end_vde_element_1-->
    </div>
    */
    var page = jQuery('body').vde_page();
    var cookieHighlightingName = page.vde_page('option', 'cookieHighlightingName');
    page.vde_page('destroy');
    Mage.Cookies.set(cookieHighlightingName, 'off');
    page = jQuery('body').vde_page();
    var commentsExist = false;
    jQuery('*').contents().each(function () {
        if (this.nodeType == Node.COMMENT_NODE) {
            if (this.data.substr(0, 9) == 'start_vde') {
                commentsExist = true;
            } else if (this.data.substr(0, 7) == 'end_vde') {
                commentsExist = true;
            }
        }
    });
    assertEquals(false, commentsExist);
}
PageTest.prototype.testHighlight = function() {
    /*:DOC +=
    <div>
        <div id="vde_element_1" class="vde_element_wrapper vde_container vde_wrapper_hidden">
            <div class="vde_element_title">Title 1</div>
        </div>
        <!--start_vde_element_1-->
        <div id="vde_element_2" class="vde_element_wrapper vde_draggable vde_wrapper_hidden">
            <div class="vde_element_title">Title 2</div>
        </div>
        <!--start_vde_element_2-->
        <div class="block block-list" id="block">
            <div class="block-title">
                <strong><span>Block Title</span></strong>
            </div>
            <div class="block-content">
                <p class="empty">Block Content</p>
            </div>
        </div>
        <!--end_vde_element_2-->
        <div id="vde_element_3" class="vde_element_wrapper vde_draggable vde_wrapper_hidden">
            <div class="vde_element_title">Title 3</div>
        </div>
        <!--end_vde_element_1-->
    </div>
    */
    jQuery.fx.off = true;
    var page = jQuery('body').vde_page();
    var cookieHighlightingName = page.vde_page('option', 'cookieHighlightingName');
    page.vde_page('destroy');
    Mage.Cookies.set(cookieHighlightingName, 'off');
    page = jQuery('body').vde_page();
    page.trigger('checked.vde_checkbox');
    var resultHierarchy = {
        vde_element_1: ['vde_element_2', 'vde_element_3'],
        vde_element_2: ['block']
    }
    var hierarchyIsCorrect = true;
    jQuery.each(resultHierarchy, function(parentKey, parentVal) {
        jQuery.each(parentVal, function(childKey, childVal) {
            if (!jQuery('#' + parentKey).has(jQuery('#' + childVal))) {
                hierarchyIsCorrect = false;
            }
        })
    });
    assertEquals(true, hierarchyIsCorrect);
    assertEquals(true, jQuery('.vde_wrapper_hidden').is(':visible'));
    assertEquals(null, Mage.Cookies.get(cookieHighlightingName));
    var highlightElementTitleSelector = page.vde_page('option', 'highlightElementTitleSelector');
    assertEquals(true, jQuery(highlightElementTitleSelector).is(':visible'));
    page.vde_page('destroy');
    jQuery.fx.off = false;
}
PageTest.prototype.testUnhighlight = function() {
    /*:DOC +=
    <div>
        <div id="vde_element_1" class="vde_element_wrapper vde_container">
            <div class="vde_element_title">Title 1</div>
            <div id="vde_element_2" class="vde_element_wrapper vde_draggable">
                <div class="vde_element_title">Title 2</div>
                <div class="block block-list block-compare" id="block">
                    <div class="block-title">
                        <strong><span>Block Title</span></strong>
                    </div>
                    <div class="block-content">
                        <p class="empty">Block Content</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    */
    jQuery.fx.off = true;
    var page = jQuery('body').vde_page();
    var highlightElementTitleSelector = page.vde_page('option', 'highlightElementTitleSelector');
    var highlightElementSelector = page.vde_page('option', 'highlightElementSelector');
    var hierarchy = {};
    jQuery(highlightElementSelector).each(function() {
        var elem = jQuery(this);
        hierarchy[elem.attr('id')] = elem.contents(':not(' + highlightElementTitleSelector + ')');
    })
    var cookieHighlightingName = page.vde_page('option', 'cookieHighlightingName');
    page.vde_page('destroy');
    Mage.Cookies.clear(cookieHighlightingName);
    page = jQuery('body').vde_page();
    page.trigger('unchecked.vde_checkbox');
    var hierarchyIsCorrect = true;
    jQuery.each(hierarchy, function(parentKey, parentVal) {
        jQuery.each(parentVal, function() {
            if (jQuery(this).parents('#' + parentKey).size()) {
                hierarchyIsCorrect = false;
            }
        })
    });
    assertEquals(true, hierarchyIsCorrect);
    assertEquals(false, jQuery('.vde_wrapper_hidden').is(':visible'));
    assertEquals('off', Mage.Cookies.get(cookieHighlightingName));
    assertEquals(false, jQuery(highlightElementTitleSelector).is(':visible'));
    page.vde_page('destroy');
    jQuery.fx.off = false;
}