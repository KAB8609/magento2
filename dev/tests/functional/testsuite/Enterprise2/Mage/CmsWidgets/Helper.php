<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsWidgets
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_CmsWidgets_Helper extends Core_Mage_CmsWidgets_Helper
{
    /**
     * Fills settings for creating widget
     *
     * @param array $settings
     */
    public function fillWidgetSettings(array $settings)
    {
        if ($settings) {
            $xpath = $this->_getControlXpath('dropdown', 'type');
            $type = $this->getValue($xpath . '/option[text()="' . $settings['type'] . '"]');
            $this->addParameter('type', str_replace('/', '-', $type));
            $packageTheme = array_map('trim', (explode('/', $settings['design_package_theme'])));
            $this->addParameter('package', $packageTheme[0]);
            $this->addParameter('theme', $packageTheme[1]);
            if (($packageTheme[0] ==  $packageTheme[1]) && ($packageTheme[1] == 'Enterprise'))
                $this->addParameter('package', 'default');
            $this->fillFieldset(array('type' => $settings['type']), 'settings_fieldset');
            $this->selectDesignPackageTheme('design_package_theme', $packageTheme[0],$packageTheme[1]);
        }
        $this->clickButton('continue', false);
        $this->pleaseWait();
        $this->validatePage('add_widget_options');
    }
        
    /**
     * Selects a store view from 'Choose Store View' drop-down in backend
     *
     * @param string $controlName Name of the dropdown from UIMaps
     * @param string $package Default = 'Enterprise'
     * @param string $theme Default = 'default'
     *
     * @throws PHPUnit_Framework_Exception
     */
    public function selectDesignPackageTheme($controlName, $package = 'Enterprise', $theme = 'default')
    {
        $fieldXpath = $this->_getControlXpath('dropdown', $controlName);
        $themeXpath = $fieldXpath
                          . "/optgroup[normalize-space(@label) = '$package']"
                          . "/option[contains(text(),'$theme')]";
        if(!$this->isElementPresent($themeXpath)) {
            throw new PHPUnit_Framework_Exception('Cannot find option ' . $themeXpath);
        }
        $optionValue = $this->getValue($themeXpath);
        //Try to select by value first, since there may be options with equal labels.
        if (isset($optionValue)) {
            $this->select($fieldXpath, 'value=' . $optionValue);
        } else {
            $this->select($fieldXpath, 'label=' . 'regexp:^\s+' . preg_quote($theme));
        }
    }

    /**
     * Fills "Widget Options" tab
     *
     * @param array $widgetOptions
     */
    public function fillWidgetOptions(array $widgetOptions)
    {
        $options = (isset($widgetOptions['chosen_option'])) ? $widgetOptions['chosen_option'] : array();
        $this->fillForm($widgetOptions, 'widgets_options');
        if ($options) {
            $this->cmsPagesHelper()->selectOptionItem($options);
        }
        if (array_key_exists('banner_name', $widgetOptions)) {
            $this->searchAndChoose(array('filter_banner_name' => $widgetOptions['banner_name']),
                'specify_banner_grid');
        }
    }
}