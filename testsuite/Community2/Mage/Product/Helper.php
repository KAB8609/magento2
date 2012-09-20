<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Helper class
 *
 */
class Community2_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    /**
     * Change attribute set
     *
     * @param array $newAttributeSet
     */
    public function changeAttributeSet($newAttributeSet)
    {
        $dropdownXpath = $this->_getControlXpath('dropdown', 'choose_attribute_set');
        $currentAttributeSet = $this->getSelectedValue($dropdownXpath);
        $fieldXpath = $this->_getControlXpath('pageelement', 'product_page_title');
        $popupXpath = $this->_getControlXpath('fieldset', 'change_attribute_set');
        $actualTitle = $this->getText($fieldXpath);
        $newTitle = str_replace($newAttributeSet, $currentAttributeSet, $actualTitle);
        $this->clickButton('change_attribute_set', false)->waitForElement($popupXpath);
        $this->fillDropdown('choose_attribute_set', $newAttributeSet);
        $this->addParameter('setId', $dropdownXpath)->clickButton('apply')->validatePage();
        $this->assertNotSame($this->getText($fieldXpath), $newTitle,
            "Attribute set in title should be $newAttributeSet, but now it's $currentAttributeSet");
    }

    /**
     * Import custom options from existent product
     *
     * @param array $productData
     */
    public function importCustomOptions(array $productData)
    {
        $this->openTab('custom_options');
        $this->clickButton('import_options', false);
        $this->waitForElement($this->_getControlXpath('fieldset', 'select_product_custom_option'));
        foreach ($productData as $value) {
            $this->searchAndChoose($value);
        }
        $this->clickButton('import', false);
        $this->pleaseWait();
    }

    /**
     * Delete all custom options
     */
    public function deleteAllCustomOptions()
    {
        $this->openTab('custom_options');
        while ($this->isElementPresent($this->_getControlXpath('fieldset', 'custom_option_set'))) {
            if (!$this->controlIsPresent('button', 'delete_custom_option')) {
                $this->fail(
                    'Current location url: ' . $this->getLocation() . "\n" . 'Current page: ' . $this->getCurrentPage()
                    . "\nProblem with 'Delete Option' button.\n" . 'Control is not present on the page');
            }
            $this->clickButton('delete_custom_option', false);
        }
    }

    /**
     * Verify Custom Options
     *
     * @param array $customOptionData
     *
     * @return boolean
     */
    public function verifyCustomOption(array $customOptionData)
    {
        $this->openTab('custom_options');
        $optionsQty = $this->getXpathCount($this->_getControlXpath('fieldset', 'custom_option_set'));
        $needCount = count($customOptionData);
        if ($needCount != $optionsQty) {
            $this->addVerificationMessage(
                'Product must be contains ' . $needCount . ' Custom Option(s), but contains ' . $optionsQty);
            return false;
        }
        $numRow = 1;
        foreach ($customOptionData as $value) {
            if (is_array($value)) {
                $optionId = $this->getOptionId($numRow);
                $this->addParameter('optionId', $optionId);
                $this->verifyForm($value, 'custom_options');
                $numRow++;
            }
        }
        return true;
    }

    /**
     * Get option id for selected row
     *
     * @param int $rowNum
     *
     * @return int
     */
    public function getOptionId($rowNum)
    {
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'custom_option_set') . "[$rowNum]";
        if ($this->isElementPresent($fieldsetXpath)) {
            $optionId = $this->getAttribute($fieldsetXpath . '@id');
            foreach (explode('_', $optionId) as $value) {
                if (is_numeric($value)) {
                    return $value;
                }
            }
        }
    }

    /**
     * Create Product method using "Add Product" split button
     *
     * @param array $productData
     * @param string $productType
     * @param bool $isSave
     */
    public function createProduct(array $productData, $productType = 'simple', $isSave = true)
    {
        $this->selectTypeProduct($productData, $productType);
        if ($productData['product_attribute_set'] != 'Default') {
            $this->changeAttributeSet($productData['product_attribute_set']);
        }
        $this->productHelper()->fillProductInfo($productData, $productType);
        if ($isSave) {
            $this->saveForm('save');
        }
    }

    /**
     * Select product type
     *
     * @param array $productData
     * @param string $productType
     */
    public function selectTypeProduct(array $productData, $productType)
    {
        $this->clickButton('add_new_product_split_select', false);
        $this->addParameter('productType', $productType);
        $this->clickControl('dropdown', 'add_product_by_type', false);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->addParameter('setId', $this->defineParameterFromUrl('set'));
        $this->validatePage();
        if ($productType == 'configurable') {
            $this->fillConfigurableSettings($productData);
        }
    }

    /**
     * Get auto-incremented SKU
     *
     * @param string $productSku
     *
     * @return string
     */
    public function getGeneratedSku($productSku)
    {
        return $productSku . '-1';
    }

    /**
     * Creating product using fields autogeneration with variables on General tab
     *
     * @param array $productData
     * @param $keyUp
     * @param array $productFields
     * @param bool $isSave
     * @param string $productType
     */
    public function createProductWithAutogeneration(array $productData, $isSave = false, $keyUp = array('general_name'),
        array $productFields = null, $productType = 'simple'
    ){
        if (!empty($productFields)) {
            foreach ($productFields as $value) {
                unset($productData[$value]);
            }
        }
        $this->productHelper()->createProduct($productData, $productType, false);
        $this->openTab('general');
        foreach ($keyUp as $key) {
            $this->keyUp($this->_getControlXpath('field', $key), ' ');
        }
        if ($isSave) {
            $this->saveForm('save');
            $this->pleaseWait();
        }
    }

    /**
     * Get value from configured mask for autogeneration
     *
     * @param $mask
     * @param array $placeholders
     *
     * @return mixed
     */
    public function formFieldValueFromMask($mask, array $placeholders)
    {
        foreach ($placeholders as $value) {
            $productField = 'general_' . str_replace(array('{{', '}}'), '', $value);
            $maskData = $this->getValue($this->_getControlXpath('field', $productField));
            $mask = str_replace($value, $maskData, $mask);
        }
        return $mask;
    }
}
