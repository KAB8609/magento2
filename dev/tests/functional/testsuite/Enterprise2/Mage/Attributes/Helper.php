<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Attributes
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
class Enterprise2_Mage_Attributes_Helper extends Mage_Selenium_TestCase
{
    /**
     * Action_helper method for Create Customer Attribute
     *
     * Preconditions: 'Manage Customer Attributes' page is opened.
     *
     * @param array $attrData Array which contains DataSet for filling of the current form
     */
    public function createAttribute($attrData)
    {
        $this->clickButton('add_new_attribute');
        $this->fillTabs($attrData);
        $this->saveForm('save_attribute');
    }

    /**
     * Filling tabs
     *
     * @param string|array $attrData
     */
    public function fillTabs($attrData)
    {
        if (is_string($attrData)) {
            $elements = explode('/', $attrData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $attrData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $propertiesTab = (isset($attrData['properties']))? $attrData['properties']: array();
        $optionsTab = (isset($attrData['manage_labels_options']))? $attrData['manage_labels_options']: array();

        $this->fillTab($propertiesTab, 'properties');
        $this->openTab('manage_labels_options');
        $this->productAttributeHelper()->storeViewTitles($optionsTab);
        $this->productAttributeHelper()->attributeOptions($optionsTab);
    }

    /**
     * Open Customer Attributes.
     * Preconditions: 'Manage Customer Attributes' page is opened.
     *
     * @param array $searchData
     */
    public function openAttribute($searchData)
    {
        $this->_prepareDataForSearch($searchData);
        $xpathTR = $this->search($searchData, 'attributes_grid');
        $this->assertNotNull($xpathTR, 'Attribute is not found');
        $cellId = $this->getColumnIdByName('Attribute Label');
        $this->addParameter('attribute_code', $this->getText($xpathTR . '//td[' . $cellId . ']'));
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR . '//td[' . $cellId . ']');
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }
}