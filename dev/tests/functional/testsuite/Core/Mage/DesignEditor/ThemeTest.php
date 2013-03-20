<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme list
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_DesignEditor_ThemeTest extends Mage_Selenium_TestCase
{
    /**
     * Store new window handle
     *
     * @var null
     */
    private $_windowId = null;

    /**
     * Preconditions:
     *
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Close additional browser window<p>
     */
    protected function tearDownAfterTest()
    {
        //Close 'New' browser window if any
        if ($this->_windowId) {
            $this->closeWindow($this->_windowId);
            $this->_windowId = null;
        }
        //Back to main window
        $this->window('');
    }

    /**
     * <p>Test theme infinity scroll on page with available themes</p>
     * @test
     */
    public function openAvailableThemePage()
    {
        $this->themeHelper()->deleteAllVirtualThemes();
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_list'));

        $xpath = $this->_getControlXpath('pageelement', 'theme_list_elements');
        $this->waitForElementOrAlert($xpath);
        $defaultElementsCount = $this->getControlCount('pageelement', 'theme_list_elements');

        /** Check that theme list loaded */
        $this->assertGreaterThan(0, $defaultElementsCount);
    }

    /**
     * <p>Test theme controls</p>
     * @test
     */
    public function themeControls()
    {
        $this->themeHelper()->deleteAllVirtualThemes();
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        $this->waitForAjax();

        /**
         * Available theme list(on first entrance)
         */
        $this->assertTrue($this->controlIsPresent('button', 'preview_demo_button'),
            'Preview button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
            'Assign button is not exists');

        $themeData = $this->themeHelper()->createTheme();
        $themeId = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);
        $this->addParameter('id', $themeId);
        /**
         * My customization theme
         */
        $this->navigate('design_editor_selector');

        $this->assertTrue($this->controlIsPresent('button', 'preview_theme_button'),
            'Preview button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
            'Assign button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'edit_theme_button'),
            'Edit button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'delete_theme_button'),
            'Delete button is not exists');

        /**
         * Available theme list
         */
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        $this->clickControl('link', 'available_themes_tab', false);
        $this->waitForAjax();

        $this->assertTrue($this->controlIsPresent('button', 'preview_demo_button'),
            'Preview button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
            'Assign button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'edit_theme_button'),
            'Edit button is not exists');

        $this->themeHelper()->deleteTheme($themeData);
    }

    /**
     * <p>Test theme selector page when customized themes present and has preview button</p>
     * @test
     */
    public function previewCustomizedTheme()
    {
        $themeData = $this->themeHelper()->createTheme();
        $themeData['id'] = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);
        $this->addParameter('id', $themeData['id']);

        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->clickButton('preview_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('preview_theme_in_navigation');
        $this->assertTrue($this->controlIsPresent('pageelement', 'vde_toolbar_row'),
            'Theme is not opened in design mode');
        $this->clickControl('link', 'quit');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('design_editor_selector');

        $this->designEditorHelper()->deleteTheme($themeData);
    }

    /**
     * <p>Test unassigned theme deleting</p>
     * @test
     */
    public function deleteUnassignedTheme()
    {
        $themeData = $this->themeHelper()->createTheme();
        $themeData['id'] = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);
        $this->addParameter('id', $themeData['id']);
        $this->navigate('design_editor_selector');

        $this->assertTrue($this->controlIsPresent('button', 'delete_theme_button'),
            'Delete button is not exists');
        $this->designEditorHelper()->deleteTheme($themeData);
    }

    /**
     * <p>Create virtual theme for tests</p>
     *
     * @test
     * @return array
     */
    public function customizeTheme()
    {
        $themeData = $this->loadDataSet('Theme', 'all_fields');
        $this->themeHelper()->createTheme($themeData);
        $themeData['id'] = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);

        return $themeData;
    }

    /**
     * Check Mode switcher button
     *
     * @param $themeData
     * @depends customizeTheme
     * @test
     */
    public function checkModeSwitcher($themeData)
    {
        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $this->addParameter('id', $themeData['id']);
        $this->clickButton('preview_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('preview_theme_in_navigation');
        $this->assertTrue($this->controlIsPresent('pageelement', 'mode_switcher'));
        $this->designEditorHelper()->selectModeSwitcher('Enabled');
        $this->validatePage('preview_theme_in_design');
        $this->assertTrue($this->controlIsPresent('pageelement', 'mode_switcher'));
        $this->designEditorHelper()->selectModeSwitcher('Disabled');
        $this->validatePage('preview_theme_in_navigation');
        $this->clickControl('link', 'quit');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('design_editor_selector');

        $this->assertTrue($this->controlIsPresent('pageelement', 'customized_themes_tab_content'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'customized_themes_tab_content'));
    }

    /**
     * Functionality changed. Will be created improvement. Test should be refactored.
     * <p>Assign theme from navigation mode</p>
     * Present one store view only
     * @param $themeData
     * @depends customizeTheme
     * @test
     */
    public function assignThemeFromNavigationMode($themeData)
    {
        $this->markTestIncomplete('Incomplete test with wrong logic. Functionality will be changed');

        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));

        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();

        $this->addParameter('id', $themeData['id']);
        $this->clickButton('edit_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('preview_theme_in_design');

        $this->designEditorHelper()->selectModeSwitcher('Disabled');
        $this->validatePage('preview_theme_in_navigation');

        $this->clickButtonAndConfirm('save_and_assign', 'confirmation_for_assign');
        $this->assertTrue($this->checkCurrentPage('assigned_theme_default_in_design'));
        $this->clickControl('link', 'quit');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('design_editor_selector');
    }

    /**
     * <p>Assign theme from design mode</p>
     * Present one store view only
     * @param $themeData
     * @depends customizeTheme
     * @test
     */
    public function assignThemeFromDesignMode($themeData)
    {
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));

        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();

        $this->addParameter('id', $themeData['id']);
        $this->clickButton('edit_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('preview_theme_in_design');

        $this->clickButton('select');
        $this->clickButtonAndConfirm('save_and_assign', 'confirmation_for_assign');
        $this->assertTrue($this->checkCurrentPage('save_and_assign_theme_default_in_design'));
        $this->clickControl('link', 'quit');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('design_editor_selector');
    }

    /**
     * <p>Test theme selector page when customized themes present and has preview button</p>
     *
     * @param $themeData
     * @depends customizeTheme
     * @test
     */
    public function assignThemeFromCustomizeTab($themeData)
    {
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));

        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();

        $this->addParameter('id', $themeData['id']);
        $this->clickButtonAndConfirm('assign_theme_button', 'confirmation_for_assign');
        $this->clickControl('link', 'quit');
        $this->addParameter('id', $themeData['id']);
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeData['id'], 'Default Store View');
        $this->elementIsPresent($xpathAssignedStoreviews);
    }

    /**
     * Check Quick Styles attributes
     * @param $themeData
     * @depends customizeTheme
     * @test
     */
    public function openAndCheckQuickStylesAttributes($themeData)
    {
        $this->navigate('design_editor_selector');
        $this->clickControl('link', 'customized_themes_tab');
        $this->addParameter('id', $themeData['id']);
        $this->clickButton('edit_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('preview_theme_in_design');
        $this->clickControl('link', 'quick_styles_doc');

        $this->openTab('header');
        $this->assertTrue($this->controlIsPresent('pageelement', 'store_name'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'background_image'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'background_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'menu_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'menu_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'menu_links'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'menu_links_hover'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'header_links'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'header_links_hover'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'scroll_bar_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'scroll_bar_handle'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'search_field'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'search_field_stroke'));

        $this->openTab('background');
        $this->assertTrue($this->controlIsPresent('pageelement', 'page_background_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'page_background_image'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_second_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_second_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_field_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_field_stroke_clicked'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'image_stroke_keylines'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'scroll_bar_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'scroll_bar_handle'));

        $this->openTab('buttons_icons');
        $this->assertTrue($this->controlIsPresent('pageelement', 'radio_button_checkboxes_icon'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'radio_button_checkboxes_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'radio_button_checkboxes_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'button_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'button_text'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'button_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'icons'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'icons_hover'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'icons_second'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'accents'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'size_swatches'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'size_swatches_unav_size'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'size_swatches_hover_selected_stroke'));

        $this->openTab('tips_messages');
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_text'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_box'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_second_text'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_second_box'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_second_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'error_box'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'error_icon'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'success_box'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'success_icon'));

        $this->openTab('fonts');
        $this->assertTrue($this->controlIsPresent('pageelement', 'baner_text_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'page_heading_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'menu_color'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'baner_text_font'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'page_heading_font'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'menu_font'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'body_text_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'buttons_color'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'body_text_font'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'buttons_font'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'text_links'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'text_links_hover'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'text_links_active'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'text_links_product_name'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'small_links'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'small_links_hover'));

        $this->clickControl('link', 'quit');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('design_editor_selector');
    }

    /**
     * Download theme css
     *
     * @param $themeData
     * @depends customizeTheme
     * @param $linkName
     * @param $fileName
     * @dataProvider allThemeCss
     * @test
     */
    public function downloadCss($fileName, $linkName, $themeData)
    {
        $this->markTestIncomplete('Problem with define correct tab');

        $this->navigate('design_editor_selector');
        $this->clickControl('link', 'customized_themes_tab');
        $this->addParameter('id', $themeData['id']);
        $this->clickButton('edit_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('preview_theme_in_design');
        $this->clickControl('link', 'code_doc');

        $this->openTab('header');
        $this->openTab('css');
        $this->clickControl('link', $linkName, false);

        $appConfig = $this->getApplicationConfig();
        if (!array_key_exists('downloadDir', $appConfig)) {
            $this->fail('downloadDir is not set in application config');
        }
        $downloadDir  = $appConfig['downloadDir'];
        $filePath = $downloadDir . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($filePath)){
            sleep(2);
            $this->assertTrue(file_exists($filePath), 'File was not downloaded');
        } else {
            $this->assertTrue(file_exists($filePath), 'File was not downloaded');
        }
    }

    public function allThemeCss()
    {
        return array(
            array('Mage_Catalog--widgets.css', 'mage_catalog_download'),
//            array('Mage_Oauth--css_oauth-simple.css', 'mage_oauth_download'),
//            array('Social_Facebook--css_facebook.css', 'social_facebook_download'),
//            array('mage_calendar.css', 'calendar_css_download'),
//            array('css_print.css', 'css_print_download'),
//            array('css_styles-ie.css', 'css_style_ie_download'),
//            array('css_styles.css', 'css_style_download'),
//            array('Mage_Cms--widgets.css', 'mage_cms_download'),
//            array('Mage_Page__css_tabs.css', 'mage_page_download'),
//            array('Mage_Reports__widgets.css', 'mage_reports_download'),
//            array('Mage_Widget__widgets.css', 'mage_widget_download')
        );
    }

    /**
     * Delete customized theme.
     * @param $themeData
     * @depends customizeTheme
     * @test
     */
    public function deleteCustomizedTheme($themeData)
    {
        $this->themeHelper()->deleteTheme($themeData);
    }
}
