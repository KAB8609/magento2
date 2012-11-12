<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Community2_Mage_Store_SingleStoreModeSystemConfigurationTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $config = $this->loadDataSet('SingleStoreMode', 'enable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    protected function tearDownAfterTest()
    {
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>Scope Selector is disabled if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration.</p>
     * <p>Expected result: </p>
     * <p>Scope Selector is not displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6180
     * @author Tatyana_Gonchar
     */
    function verificationScopeSelector()
    {
        $this->admin('system_configuration');
        $this->assertFalse($this->controlIsPresent('fieldset', 'current_configuration_scope'),
               "Scope Selector is present on the page");
    }

    /**
     * <p>"Export Table Rates" functionality is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration - Sales - Shipping Methods.</p>
     * <p>5.Check for "Table Rates" fieldset  </p>
     * <p>Expected result: </p>
     * <p>"Export CSV" button is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6181
     * @author Tatyana_Gonchar
     */
    function verificationTableRatesExport()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('sales_shipping_methods');
        $this->assertTrue($this->buttonIsPresent('table_rates_export_csv'),
               "Button Export CSV is not present on the page");
    }

    /**
     * <p>"Account Sharing Options" fieldset is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration - Customer - Customer Configuration</p>
     * <p>5.Check for "Account Sharing Options" fieldset  </p>
     * <p>Expected result:</p>
     * <p>"Account Sharing Options" fieldset is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6182
     * @author Tatyana_Gonchar
     */
    function verificationAccountSharingOptions()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('customers_customer_configuration');
        $fieldset = 'account_sharing_options';
        $this->assertFalse($this->controlIsPresent('fieldset', $fieldset), "Fieldset $fieldset is present on the page");
    }

    /**
     * <p>"Price" fieldset is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration - Catalog - Catalog</p>
     * <p>5.Check for "Price" fieldset</p>
     * <p>Expected result: </p>
     * <p>"Price" fieldset is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6183
     * @author Tatyana_Gonchar
     */
    function verificationCatalogPrice()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_catalog');
        $this->assertFalse($this->controlIsPresent('fieldset','price'), "Fieldset Price is not present on the page");
    }

    /**
     * <p>"Debug" fieldset is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration - Advanced - Developer</p>
     * <p>5.Check for "Debug" fieldset.</p>
     * <p>Expected result:</p>
     * <p>"Debug" fieldset is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6184
     * @author Tatyana_Gonchar
     */
    function verificationDebugOptions()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('advanced_developer');
        $this->assertTrue($this->controlIsPresent('fieldset', 'debug'), "Fieldset Debug is not present on the page");
    }

    /**
     *<p>Hints for fields are disabled if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration</p>
     * <p>5.Open required tab and fieldset and check hints</p>
     * <p>Expected result: </p>
     * <p>Hints are not displayed</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6185
     * @author Tatyana_Gonchar
     */
    function verificationHints()
    {
        $this->admin('system_configuration');
        $storeView = $this->_getControlXpath('pageelement', 'store_view_hint');
        $globalView = $this->_getControlXpath('pageelement', 'global_view_hint');
        $websiteView = $this->_getControlXpath('pageelement', 'website_view_hint');
        $tabs = $this->getCurrentUimapPage()->getMainForm()->getAllTabs();
        foreach ($tabs as $tab => $value) {
            $uimapFields = array();
            $this->openTab($tab);
            $uimapFields[self::FIELD_TYPE_MULTISELECT] = $value->getAllMultiselects();
            $uimapFields[self::FIELD_TYPE_DROPDOWN] = $value->getAllDropdowns();
            $uimapFields[self::FIELD_TYPE_INPUT] = $value->getAllFields();
            foreach ($uimapFields as $element) {
                foreach ($element as $name => $xpath) {
                    if ($this->isElementPresent($xpath . $storeView)) {
                        $this->addVerificationMessage("Element $name is on the page");
                    }
                    if ($this->isElementPresent($xpath . $globalView)) {
                        $this->addVerificationMessage("Element $name is on the page");
                    }
                    if ($this->isElementPresent($xpath . $websiteView)) {
                        $this->addVerificationMessage("Element $name is on the page");
                    }
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}