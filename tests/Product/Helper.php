<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_Helper extends Mage_Selenium_TestCase
{

    /**
     * Fill in Product Settings tab
     *
     * @param array $productData
     * @param string $productType Value - simple|virtual|bundle|configurable|downloadable|grouped
     */
    public function fillProductSettings($productData, $productType='simple')
    {
        $attributeSet = (isset($productData['product_attribute_set']))
                            ? $productData['product_attribute_set']
                            : null;

        $attributeSetXpath = $this->_getControlXpath('dropdown', 'product_attribute_set');
        $productTypeXpath = $this->_getControlXpath('dropdown', 'product_type');

        if (!empty($attributeSet)) {
            $this->select($attributeSetXpath, 'label=' . $attributeSet);
            $attributeSetID = $this->getValue($attributeSetXpath . '/option[text()=\'' . $attributeSet . '\']');
        } else {
            $attributeSetID = $this->getValue($attributeSetXpath . "/option[@selected='selected']");
        }
        $this->select($productTypeXpath, 'value=' . $productType);

        $productParameters = 'set/' . $attributeSetID . '/type/' . $productType . '/';
        $this->addParameter('productParameters', $productParameters);

        $this->clickButton('continue');
    }

    /**
     * Select Dropdown Attribute(s) for configurable product creation
     *
     * @param array $productData
     */
    public function fillConfigurableSettings(array $productData)
    {
        $productParameters = $this->_paramsHelper->getParameter('productParameters');

        $attributes = (isset($productData['configurable_attribute_title']))
                        ? explode(',', $productData['configurable_attribute_title'])
                        : null;

        if (!empty($attributes)) {
            $attributesId = array();
            $attributes = array_map('trim', $attributes);

            foreach ($attributes as $attributeTitle) {
                $this->addParameter('attributeTitle', $attributeTitle);
                $xpath = $this->_getControlXpath('checkboxe', 'configurable_attribute_title');
                if ($this->isElementPresent($xpath)) {
                    $attributesId[] = $this->getAttribute($xpath . '/@value');
                    $this->click($xpath);
                } else {
                    $this->fail("Dropdown attribute with title '$attributeTitle' is not present on the page");
                }
            }

            $attributesUrl = urlencode(base64_encode(implode(',', $attributesId)));
            $productParameters = 'attributes/' . $attributesUrl . '/' . $productParameters;
            $this->addParameter('productParameters', $productParameters);

            $this->clickButton('continue');
        } else {
            $this->fail('Dropdown attribute for configurable product creation is not set');
        }
    }

    /**
     * Fill Product Tab
     *
     * @param array $productData
     * @param string $tabName Value - general|prices|meta_information|images|recurring_profile
     * |design|gift_options|inventory|websites|categories|related|up_sells
     * |cross_sells|custom_options|bundle_items|associated|downloadable_information
     */
    public function fillTab(array $productData, $tabName = 'general')
    {
        $tabData = array();
        $needFilling = false;
        $waitAjax = false;

        foreach ($productData as $key => $value) {
            if (preg_match('/^' . $tabName . '/', $key)) {
                $tabData[$key] = $value;
            }
        }

        if ($tabData) {
            $needFilling = true;
        }

        $tabXpath = $this->getCurrentLocationUimapPage()->findTab($tabName)->getXpath();
        if ($tabName == 'websites' && !$this->isElementPresent($tabXpath)) {
            $needFilling = false;
        }

        if (!$needFilling) {
            return true;
        }

        $isTabOpened = $this->getAttribute($tabXpath . '/parent::*/@class');
        if (!preg_match('/active/', $isTabOpened)) {
            if (preg_match('/ajax/', $isTabOpened)) {
                $waitAjax = true;
            }
            $this->clickControl('tab', $tabName, false);
            if ($waitAjax) {
                $this->pleaseWait();
            }
        }

        switch ($tabName) {
            case 'prices':
                $arrayKey = 'prices_tier_price_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $key => $value) {
                        $this->addTierPrice($value);
                    }
                }
                $this->fillForm($tabData, 'prices');
                $this->fillUserAttributesOnTab($tabData, $tabName);
                break;
            case 'websites':
                $websites = explode(',', $tabData[$tabName]);
                $websites = array_map('trim', $websites);
                foreach ($websites as $value) {
                    $this->selectWebsite($value);
                }
                break;
            case 'categories':
                $categories = explode(',', $tabData[$tabName]);
                $categories = array_map('trim', $categories);
                foreach ($categories as $value) {
                    $this->categoryHelper()->selectCategory($value);
                }
                break;
            case 'related': case 'up_sells': case 'cross_sells':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $key => $value) {
                        $this->assignProduct($value, $tabName);
                    }
                }
                break;
            case 'custom_options':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $key => $value) {
                        $this->addCustomOption($value);
                    }
                }
                break;
            case 'bundle_items':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    if (array_key_exists('ship_bundle_items', $tabData[$arrayKey])) {
                        $array['ship_bundle_items'] = $tabData[$arrayKey]['ship_bundle_items'];
                        $this->fillForm($array, 'bundle_items');
                    }
                    foreach ($tabData[$arrayKey] as $key => $value) {
                        if (is_array($value)) {
                            $this->addBundleOption($value);
                        }
                    }
                }
                break;
            case 'associated':
                $arrayKey = $tabName . '_grouped_data';
                $arrayKey1 = $tabName . '_configurable_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $key => $value) {
                        $this->assignProduct($value, $tabName);
                    }
                } elseif (array_key_exists($arrayKey1, $tabData) && is_array($tabData[$arrayKey1])) {
                    $attributeTitle = (isset($productData['configurable_attribute_title']))
                                        ? $productData['configurable_attribute_title']
                                        : null;
                    if (!$attributeTitle) {
                        $this->fail('Attribute Title for configurable product is not set');
                    }
                    $this->addParameter('attributeTitle', $attributeTitle);
                    $this->fillForm($tabData[$arrayKey1], $tabName);
                    foreach ($tabData[$arrayKey1] as $key => $value) {
                        if (is_array($value)) {
                            $this->assignProduct($value, $tabName, $attributeTitle);
                        }
                    }
                }
                break;
            case 'downloadable_information':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $key => $value) {
                        if (preg_match('/^downloadable_sample_/', $key) && is_array($value)) {
                            $this->addDownloadableOption($value, 'sample');
                        }
                        if (preg_match('/^downloadable_link_/', $key) && is_array($value)) {
                            $this->addDownloadableOption($value, 'link');
                        }
                    }
                }
                $this->fillForm($tabData[$arrayKey], $tabName);
                break;
            default:
                $this->fillForm($tabData, $tabName);
                $this->fillUserAttributesOnTab($tabData, $tabName);
                break;
        }
    }

    /**
     * Add Tier Price
     *
     * @param array $tierPriceData
     */
    public function addTierPrice(array $tierPriceData)
    {
        $page = $this->getCurrentLocationUimapPage();
        $tierRowXpath = $page->findFieldset('tier_price_row')->getXpath();
        $rowNumber = $this->getXpathCount($tierRowXpath);
        $this->addParameter('tierPriceId', $rowNumber);
        $page->assignParams($this->_paramsHelper);
        $this->clickButton('add_tier_price', FALSE);
        $this->fillForm($tierPriceData, 'prices');
    }

    /**
     * Add Custom Option
     *
     * @param array $customOptionData
     */
    public function addCustomOption(array $customOptionData)
    {
        $page = $this->getCurrentLocationUimapPage();
        $fieldSetXpath = $page->findFieldset('custom_option_set')->getXpath();
        $optionId = $this->getXpathCount($fieldSetXpath) + 1;
        $this->addParameter('optionId', $optionId);
        $this->clickButton('add_option', FALSE);
        $this->fillForm($customOptionData, 'custom_options');
        foreach ($customOptionData as $row_key => $row_value) {
            if (preg_match('/^custom_option_row/', $row_key) && is_array($row_value)) {
                $rowId = $this->getXpathCount($fieldSetXpath . "//tr[contains(@id,'product_option_')][not(@style)]");
                $this->addParameter('rowId', $rowId);
                $this->clickButton('add_row', FALSE);
                $this->fillForm($row_value, 'custom_options');
            }
        }
    }

    /**
     * Select Website by Website name
     *
     * @param type $websiteName
     */
    public function selectWebsite($websiteName, $action='select')
    {
        $fieldsetXpath = $this->getCurrentLocationUimapPage()->findFieldset('product_websites')->getXpath();
        $this->addParameter('websiteName', $websiteName);
        $websiteXpath = $fieldsetXpath . $this->_getControlXpath('checkboxe', 'websites');
        if ($this->isElementPresent($websiteXpath)) {
            if ($this->getValue($websiteXpath) == 'off') {
                switch ($action) {
                    case 'select':
                        $this->click($websiteXpath);
                        break;
                    case 'verify':
                        $this->messages['error'][] = 'Website with name "' . $websiteName . '" is not selected';
                        break;
                }
            }
        } else {
            $this->fail('Website with name "' . $websiteName . '" does not exist');
        }
    }

    /**
     * Assign product. Use for fill in 'Related Products', 'Up-sells' or 'Cross-sells' tabs
     *
     * @param array $data
     * @param string $tabName
     * @param string $attributeTitle
     */
    public function assignProduct(array $data, $tabName, $attributeTitle = null)
    {
        $fillingData = array();

        foreach ($data as $key => $value) {
            if (!preg_match('/^' . $tabName . '_search_/', $key)) {
                $fillingData[$key] = $value;
                unset($data[$key]);
            }
        }

        if ($attributeTitle) {
            $attributeCode = $this->getAttribute("//a[span[text()='$attributeTitle']]/@name");
            $this->addParameter('attributeCode', $attributeCode);
            $this->addParameter('attributeTitle', $attributeTitle);
        }
        $this->searchAndChoose($data, $tabName);
        //Fill in additional data
        if ($fillingData) {
            $xpathTR = $this->formSearchXpath($data);
            if ($attributeTitle) {
                $number = $this->findColumnNumberByName($attributeTitle, 'associated');
                $setXpath = $this->_getControlXpath('fieldset', 'associated');
                $attributeValue = $this->getText($setXpath . $xpathTR . "//td[$number]");
                $this->addParameter('attributeValue', $attributeValue);
            } else {
                $this->addParameter('productXpath', $xpathTR);
            }
            $this->fillForm($fillingData, $tabName);
        }
    }

    /**
     * Add Bundle Option
     *
     * @param array $bundleOptionData
     */
    public function addBundleOption(array $bundleOptionData)
    {
        $fieldSetXpath = $this->getCurrentLocationUimapPage()->findFieldset('bundle_items')->getXpath();
        $optionsCount = $this->getXpathCount($fieldSetXpath . "//div[@class='option-box']");
        $this->addParameter('optionId', $optionsCount);
        $this->clickButton('add_new_option', FALSE);
        $this->fillForm($bundleOptionData, 'bundle_items');
        foreach ($bundleOptionData as $key => $value) {
            $productSearch = array();
            $selectionSettings = array();
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if ($k == 'bundle_items_search_name' or $k == 'bundle_items_search_sku') {
                        $this->addParameter('productSku', $v);
                    }
                    if (preg_match('/^bundle_items_search_/', $k)) {
                        $productSearch[$k] = $v;
                    } elseif ($k == 'bundle_items_qty_to_add') {
                        $selectionSettings['selection_item_default_qty'] = $v;
                    } elseif (preg_match('/^selection_item_/', $k)) {
                        $selectionSettings[$k] = $v;
                    }
                }
                if ($productSearch) {
                    $this->clickButton('add_selection', FALSE);
                    $this->pleaseWait();
                    $this->searchAndChoose($productSearch, 'select_product_to_bundle_option');
                    $this->clickButton('add_selected_products', FALSE);
                    if ($selectionSettings) {
                        $this->fillForm($selectionSettings, 'new_bundle_option');
                    }
                }
            }
        }
    }

    /**
     * Add Sample for Downloadable product
     *
     * @param array $optionData
     * @param string $type
     */
    public function addDownloadableOption(array $optionData, $type)
    {
        $fieldSet = $this->_getControlXpath('link', 'downloadable_' . $type);
        if (!$this->isElementPresent($fieldSet . "/parent::*[normalize-space(@class)='open']")) {
            $this->clickControl('link', 'downloadable_' . $type, FALSE);
        }

        $fieldSetXpath = $this->getCurrentLocationUimapPage()->findFieldset('downloadable_' . $type)->getXpath();
        $rowNumber = $this->getXpathCount($fieldSetXpath . "//*[@id='" . $type . "_items_body']/tr");
        $this->addParameter('rowId', $rowNumber);
        $this->clickButton('downloadable_' . $type . '_add_new_row', FALSE);
        $this->fillForm($optionData, 'downloadable_information');
    }

    /**
     * Fill user product attribute
     *
     * @param array $productData
     * @param type $tabName
     */
    public function fillUserAttributesOnTab(array $productData, $tabName)
    {
        $userFieldData = $tabName . '_user_attr';
        if (array_key_exists($userFieldData, $productData) && is_array($productData[$userFieldData])) {
            foreach ($productData[$userFieldData] as $fieldType => $dataArray) {
                if (is_array($dataArray)) {
                    foreach ($dataArray as $fieldKey => $fieldValue) {
                        $this->addParameter('attibuteCode' . ucfirst(strtolower($fieldType)), $fieldKey);
                        $xpath = $this->_getControlXpath($fieldType, $tabName . '_user_attr_' . $fieldType);
                        switch ($fieldType) {
                            case 'dropdown':
                                $this->select($xpath, $fieldValue);
                                break;
                            case 'field':
                                $this->type($xpath, $fieldValue);
                                break;
                            case 'multiselect':
                                $this->removeAllSelections($xpath);
                                $values = explode(',', $fieldValue);
                                $values = array_map('trim', $values);
                                foreach ($values as $v) {
                                    $this->addSelection($xpath, $v);
                                }
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Find Column Number in table by Name
     *
     * @param type $columnName
     * @param type $fieldSetName
     * @return int
     */
    public function findColumnNumberByName($columnName, $fieldSetName = null)
    {
        if ($fieldSetName != null) {
            $fieldSetXpath = $this->getCurrentUimapPage()->getMainForm()->findFieldset($fieldSetName)->getXpath();
        } else {
            $fieldSetXpath = '';
        }
        $columnXpath = $fieldSetXpath . "//table[@class='data']//tr[@class='headings']/th";
        $columnQty = $this->getXpathCount($columnXpath);
        for ($i = 1; $i <= $columnQty; $i++) {
            $text = $this->getText($columnXpath . "[$i]");
            if ($text == $columnName) {
                return $i;
            }
        }
        return 0;
    }

    /**
     * Create Product
     *
     * @param array $productData
     * @param string $productType
     */
    public function createProduct(array $productData, $productType='simple')
    {
        $productData = $this->arrayEmptyClear($productData);
        $this->clickButton('add_new_product');
        $this->fillProductSettings($productData, $productType);
        if ($productType == 'configurable') {
            $this->fillConfigurableSettings($productData);
        }
        $this->fillProductInfo($productData, $productType);
        $this->clickButton('save', false);
        $this->waitForElement(array(self::xpathErrorMessage .
                                            "[not(text()='".self::excludedBundleMessage."')]" .
                                            "[not(text()='".self::excludedConfigurableMessage."')]",
                                         self::xpathValidationMessage,
                                         self::xpathSuccessMessage));
    }

    /**
     * Fill Product info
     *
     * @param array $productData
     * @param string $productType
     */
    public function fillProductInfo(array $productData, $productType='simple')
    {
        $this->fillTab($productData);
        $this->fillTab($productData, 'prices');
        $this->fillTab($productData, 'meta_information');
        //@TODO Fill in Images Tab
        if ($productType == 'simple' || $productType == 'virtual') {
            $this->fillTab($productData, 'recurring_profile');
        }
        $this->fillTab($productData, 'design');
        $this->fillTab($productData, 'gift_options');
        $this->fillTab($productData, 'inventory');
        $this->fillTab($productData, 'websites');
        $this->fillTab($productData, 'categories');
        $this->fillTab($productData, 'related');
        $this->fillTab($productData, 'up_sells');
        $this->fillTab($productData, 'cross_sells');
        $this->fillTab($productData, 'custom_options');
        if ($productType == 'grouped' || $productType == 'configurable') {
            $this->fillTab($productData, 'associated');
        }
        if ($productType == 'bundle') {
            $this->fillTab($productData, 'bundle_items');
        }
        if ($productType == 'downloadable') {
            $this->fillTab($productData, 'downloadable_information');
        }
    }

    /**
     * Open product.
     *
     * @param array $productSearch
     */
    public function openProduct(array $productSearch)
    {
        $this->assertTrue($this->searchAndOpen($productSearch), 'Product is not found');
    }

    /**
     * Verify product info
     *
     * @param array $productData
     */
    public function verifyProductInfo(array $productData, $skipElements = null)
    {
        $productData = $this->arrayEmptyClear($productData);
        $nestedArrays = array();
        foreach ($productData as $key => $value) {
            if (is_array($value)) {
                $nestedArrays[$key] = $value;
                unset($productData[$key]);
            }
            if ($key == 'websites' or $key == 'categories') {
                $nestedArrays[$key] = $value;
                unset($productData[$key]);
            }
        }
        $this->verifyForm($productData, null, $skipElements);
        // Verify tier prices
        if (array_key_exists('prices_tier_price_data', $nestedArrays)) {
            $this->verifyTierPrices($nestedArrays['prices_tier_price_data']);
        }
        //Verify selected websites
        if (array_key_exists('websites', $nestedArrays)) {
            $tabXpath = $this->getCurrentLocationUimapPage()->findTab('websites')->getXpath();
            if ($this->isElementPresent($tabXpath)) {
                $websites = explode(',', $nestedArrays['websites']);
                $websites = array_map('trim', $websites);
                foreach ($websites as $value) {
                    $this->selectWebsite($value, 'verify');
                }
            }
        }
        //Verify selected categories
        if (array_key_exists('categories', $nestedArrays)) {
            $categories = explode(',', $nestedArrays['categories']);
            $categories = array_map('trim', $categories);
            $this->clickControl('tab', 'categories', false);
            $this->pleaseWait();
            foreach ($categories as $value) {
                $this->isSelectedCategory($value);
            }
        }
        //Verify assigned products for 'Related Products', 'Up-sells', 'Cross-sells' tabs
        if (array_key_exists('related_data', $nestedArrays)) {
            $this->clickControl('tab', 'related', false);
            $this->pleaseWait();
            foreach ($nestedArrays['related_data'] as $key => $value) {
                $this->isAssignedProduct($value, 'related');
            }
        }
        if (array_key_exists('up_sells_data', $nestedArrays)) {
            $this->clickControl('tab', 'up_sells', false);
            $this->pleaseWait();
            foreach ($nestedArrays['up_sells_data'] as $key => $value) {
                $this->isAssignedProduct($value, 'up_sells');
            }
        }
        if (array_key_exists('cross_sells_data', $nestedArrays)) {
            $this->clickControl('tab', 'cross_sells', false);
            $this->pleaseWait();
            foreach ($nestedArrays['cross_sells_data'] as $key => $value) {
                $this->isAssignedProduct($value, 'cross_sells');
            }
        }
        // Verify Associated Products tab
        if (array_key_exists('associated_grouped_data', $nestedArrays)) {
            $this->clickControl('tab', 'associated', false);
            $this->pleaseWait();
            foreach ($nestedArrays['associated_grouped_data'] as $key => $value) {
                $this->isAssignedProduct($value, 'associated');
            }
        }
        if (array_key_exists('associated_configurable_data', $nestedArrays)) {
            $this->clickControl('tab', 'associated', false);
            $this->pleaseWait();
            $attributeTitle = (isset($productData['configurable_attribute_title']))
                                ? $productData['configurable_attribute_title']
                                : null;
            if (!$attributeTitle) {
                $this->fail('Attribute Title for configurable product is not set');
            }
            $this->addParameter('attributeTitle', $attributeTitle);
            $this->verifyForm($nestedArrays['associated_configurable_data'], 'associated');
            foreach ($nestedArrays['associated_configurable_data'] as $key => $value) {
                if (is_array($value)) {
                    $this->isAssignedProduct($value, 'associated', $attributeTitle);
                }
            }
        }
        if (array_key_exists('custom_options_data', $nestedArrays)) {
            $this->verifyCustomOption($nestedArrays['custom_options_data']);
        }
        if (array_key_exists('bundle_items_data', $nestedArrays)) {
            $this->verifyBundleOptions($nestedArrays['bundle_items_data']);
        }
        if (array_key_exists('downloadable_information_data', $nestedArrays)) {
            $samples = array();
            $links = array();
            foreach ($nestedArrays['downloadable_information_data'] as $key => $value) {
                if (preg_match('/^downloadable_sample_/', $key) && is_array($value)) {
                    $samples[$key] = $value;
                }
                if (preg_match('/^downloadable_link_/', $key) && is_array($value)) {
                    $links[$key] = $value;
                }
            }
            if ($samples) {
                $this->verifyDownloadableOptions($samples, 'sample');
            }
            if ($links) {
                $this->verifyDownloadableOptions($links, 'link');
            }
            $this->verifyForm($nestedArrays['downloadable_information_data'], 'downloadable_information');
        }
        // Error Output
        if (!empty($this->messages['error'])) {
            $this->fail(implode("\n", $this->messages['error']));
        }
    }

    /**
     * Verify Tier Prices
     *
     * @param array $tierPriceData
     */
    public function verifyTierPrices(array $tierPriceData)
    {
        $page = $this->getCurrentLocationUimapPage();
        $fieldSetXpath = $page->findFieldset('tier_price_row')->getXpath();
        $rowQty = $this->getXpathCount($fieldSetXpath);
        $needCount = count($tierPriceData);
        if ($needCount != $rowQty) {
            $this->messages['error'][] = 'Product must be contains ' . $needCount
                    . 'Tier Price(s), but contains ' . $rowQty;
            return false;
        }
        $i = 0;
        foreach ($tierPriceData as $key => $value) {
            $this->addParameter('tierPriceId', $i);
            $page->assignParams($this->_paramsHelper);
            $this->verifyForm($value, 'prices');
            $i++;
        }
        return true;
    }

    /**
     * Verify that category is selected
     *
     * @param string $categotyPath
     */
    public function isSelectedCategory($categotyPath)
    {
        $nodes = explode('/', $categotyPath);
        $rootCat = array_shift($nodes);

        $correctRoot = $this->categoryHelper()->defineCorrectCategory($rootCat);

        foreach ($nodes as $value) {
            $correctSubCat = array();

            for ($i = 0; $i < count($correctRoot); $i++) {
                $correctSubCat = array_merge($correctSubCat,
                        $this->categoryHelper()->defineCorrectCategory($value, $correctRoot[$i]));
            }
            $correctRoot = $correctSubCat;
        }

        if ($correctRoot) {
            $catXpath = '//*[@id=\'' . array_shift($correctRoot) . '\']/parent::*/input';
            if ($this->getValue($catXpath) == 'off') {
                $this->messages['error'][] = 'Category with path: "' . $categotyPath . '" is not selected';
            }
        } else {
            $this->fail("Category with path='$categotyPath' not found");
        }
    }

    /**
     * Verify that product is assigned
     *
     * @param array $data
     * @param string $fieldSetName
     * @param string $attributeTitle
     */
    public function isAssignedProduct(array $data, $fieldSetName, $attributeTitle=null)
    {
        $fillingData = array();

        foreach ($data as $key => $value) {
            if (!preg_match('/^' . $fieldSetName . '_search_/', $key)) {
                $fillingData[$key] = $value;
                unset($data[$key]);
            }
        }

        if ($attributeTitle) {
            $attributeCode = $this->getAttribute("//a[span[text()='$attributeTitle']]/@name");
            $this->addParameter('attributeCode', $attributeCode);
            $this->addParameter('attributeTitle', $attributeTitle);
        }
        $xpathTR = $this->formSearchXpath($data);
        $fieldSetXpath = $this->getCurrentLocationUimapPage()->getMainForm()->findFieldset($fieldSetName)->getXpath();

        if (!$this->isElementPresent($fieldSetXpath . $xpathTR)) {
            $this->messages['error'][] = $fieldSetName . " tab: Product is not assigned with data: \n"
                    . print_r($data, true);
        } else {
            if ($fillingData) {
                if ($attributeTitle) {
                    $number = $this->findColumnNumberByName($attributeTitle, 'associated');
                    $attributeValue = $this->getText($xpathTR . "//td[$number]");
                    $this->addParameter('attributeValue', $attributeValue);
                } else {
                    $this->addParameter('productXpath', $xpathTR);
                }
                $this->verifyForm($fillingData, $fieldSetName);
            }
        }
    }

    /**
     * Verify Custom Options
     *
     * @param array $customOptionData
     */
    public function verifyCustomOption(array $customOptionData)
    {
        $this->clickControl('tab', 'custom_options', false);
        $this->pleaseWait();
        $page = $this->getCurrentLocationUimapPage();
        $fieldSetXpath = $page->findFieldset('custom_option_set')->getXpath();
        $optionsQty = $this->getXpathCount($fieldSetXpath);
        $needCount = count($customOptionData);
        if ($needCount != $optionsQty) {
            $this->messages['error'][] = 'Product must be contains ' . $needCount
                    . ' Custom Option(s), but contains ' . $optionsQty;
            return false;
        }
        $id = $this->getAttribute($fieldSetXpath . "[1]/@id");
        $id = explode('_', $id);
        foreach ($id as $value) {
            if (is_numeric($value)) {
                $optionId = $value;
            }
        }
        // @TODO Need implement full verification for custom options with type = select (not tested rows)
        foreach ($customOptionData as $value) {
            if (is_array($value)) {
                $this->addParameter('optionId', $optionId);
                $this->verifyForm($value, 'custom_options');
                $optionId--;
            }
        }
        return true;
    }

    /**
     * verify Bundle Options
     *
     * @param array $bundleData
     */
    public function verifyBundleOptions(array $bundleData)
    {
        $this->clickControl('tab', 'bundle_items', false);
        $this->pleaseWait();
        $fieldSetXpath = $this->getCurrentLocationUimapPage()->findFieldset('bundle_items')->getXpath();
        $optionSet = $fieldSetXpath . "//div[@class='option-box']";
        $optionsCount = $this->getXpathCount($optionSet);
        $needCount = count($bundleData);
        if (array_key_exists('ship_bundle_items', $bundleData)) {
            $needCount = $needCount - 1;
        }
        if ($needCount != $optionsCount) {
            $this->messages['error'][] = 'Product must be contains ' . $needCount
                    . 'Bundle Item(s), but contains ' . $optionsCount;
            return false;
        }

        $i = 0;
        foreach ($bundleData as $option => $values) {
            if (is_string($values)) {
                $this->verifyForm(array($option => $values), 'bundle_items');
            }
            if (is_array($values)) {
                $this->addParameter('optionId', $i);
                $this->verifyForm($values, 'bundle_items');
                foreach ($values as $k => $v) {
                    if (preg_match('/^bundle_items_add_product/', $k) && is_array($v)) {
                        $selectionSettings = array();
                        foreach ($v as $field => $data) {
                            if ($field == 'bundle_items_search_name' or $field == 'bundle_items_search_sku') {
                                $productSku = $data;
                            }
                            if (!preg_match('/^bundle_items_search/', $field)) {
                                if ($field == 'bundle_items_qty_to_add') {
                                    $selectionSettings['selection_item_default_qty'] = $data;
                                } else {
                                    $selectionSettings[$field] = $data;
                                }
                            }
                        }
                        $k = $i + 1;
                        if (!$this->isElementPresent($optionSet . "[$k]"
                                        . "//tr[@class='selection' and contains(.,'$productSku')]")) {
                            $this->messages['error'][] = "Product with sku(name)'" . $productSku
                                    . "' is not assigned to bundle item $i";
                        } else {
                            if ($selectionSettings) {
                                $this->addParameter('productSku', $productSku);
                                $this->verifyForm($selectionSettings, 'bundle_items');
                            }
                        }
                    }
                }
                $i++;
            }
        }
        return true;
    }

    /**
     * Verify Downloadable Options
     *
     * @param array $optionsData
     * @param string $type
     * @return type
     */
    public function verifyDownloadableOptions(array $optionsData, $type)
    {
        $fieldSetXpath = $this->getCurrentLocationUimapPage()->findFieldset('downloadable_' . $type)->getXpath();
        $rowQty = $this->getXpathCount($fieldSetXpath . "//*[@id='" . $type . "_items_body']/tr");
        $needCount = count($optionsData);
        if ($needCount != $rowQty) {
            $this->messages['error'][] = 'Product must be contains ' . $needCount
                    . ' Downloadable ' . $type . '(s), but contains ' . $rowQty;
            return false;
        }
        $i = 0;
        foreach ($optionsData as $value) {
            $this->addParameter('rowId', $i);
            $this->verifyForm($value, 'downloadable_information');
            $i++;
        }
        return true;
    }

    #*******************************************
    #*         Frontend Helper Methods         *
    #*******************************************

    /**
     * Open product on FrontEnd
     *
     * @param array $productName
     */
    public function frontOpenProduct($productName)
    {
        if (is_array($productName)) {
            if (array_key_exists('general_name', $productName)) {
                $productName = $productName['general_name'];
            } else {
                $this->fail('Insufficient data to open a product');
            }
        }
        $productUrl = preg_replace('#[^0-9a-z]+#i', '-', $productName);
        $productUrl = strtolower($productUrl);
        $productUrl = trim($productUrl, '-');

        $this->addParameter('productUrl', $productUrl);
        $this->getUimapPage('frontend', 'product_page')->assignParams($this->_paramsHelper);

        $this->frontend('product_page');
        $xpathName = $this->getCurrentLocationUimapPage()->getMainForm()->findPageelement('produc_name');
        $openedProductName = $this->getText($xpathName);
        $this->assertEquals($productName, $openedProductName,
                "Product with name '$openedProductName' is opened, but should be '$productName'");
    }

    /**
     * Add product to shopping cart
     *
     * @param array|null $dataForBuy
     */
    public function frontAddProductToCart($dataForBuy = null)
    {
        $productType = $this->frontDetectProductType();
        if ($dataForBuy) {
            $this->frontFillBuyInfo($dataForBuy, $productType);
        }
        $xpathName = $this->getCurrentLocationUimapPage()->getMainForm()->findPageelement('produc_name');
        $openedProductName = $this->getText($xpathName);
        $this->addParameter('productName', $openedProductName);
        $this->clickButton('add_to_cart');
    }

    /**
     * @TODO
     */
    public function frontDetectProductType()
    {

    }

    /**
     * @TODO
     */
    public function frontFillBuyInfo()
    {

    }

}
