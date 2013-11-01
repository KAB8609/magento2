<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CompareProducts
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
class Core_Mage_CompareProducts_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Add product from Catalog page
     *
     *
     * @param array $productName  Name of product to be added
     * @param array $categoryName  Products Category
     */
    public function frontAddToCompareFromCatalogPage($productName, $categoryName)
    {
        if (!$this->categoryHelper()->frontSearchAndOpenPageWithProduct($productName, $categoryName)) {
            $this->fail('Could not find the product');
        }
        $this->clickControl('link', 'add_to_compare');
    }

    /**
     * Add product from Product page
     *
     * @param array $productName  Name of product to be added
     */
    public function frontAddToCompareFromProductPage($productName)
    {
        $this->productHelper()->frontOpenProduct($productName);
        $this->clickControl('link', 'add_to_compare');
    }

    /**
     * Removes all products from the Compare Products widget
     *
     * Preconditions: page with Compare Products widget should be opened
     *
     * @return bool Returns False if the operation could not be performed
     * or the compare block is not present on the page
     */
    public function frontClearAll()
    {
        if (!$this->controlIsPresent('pageelement', 'compare_block_title')) {
            return false;
        }
        if ($this->controlIsPresent('link', 'compare_clear_all')) {
            $this->clickControlAndConfirm('link', 'compare_clear_all', 'confirmation_clear_all_from_compare');
        }
        return true;
    }

    /**
     * Removes product from the Compare Products block
     * Preconditions: page with Compare Products block is opened
     *
     * @param string $productName Name of product to be deleted
     */
    public function frontRemoveProductFromCompareBlock($productName)
    {
        $this->addParameter('productName', $productName);
        $this->clickControlAndConfirm('link', 'compare_delete_product',
            'confirmation_for_removing_product_from_compare');
    }

    /**
     * Removes product from the Compare Products pop-up
     * Preconditions: Compare Products pop-up is opened
     *
     * @param string $productName Name of product to be deleted
     *
     * @return bool
     */
    public function frontRemoveProductFromComparePopup($productName)
    {
        $compareProducts = $this->frontGetProductsListComparePopup();
        if (array_key_exists($productName, $compareProducts) and count($compareProducts) >= 3) {
            $this->addParameter('columnIndex', $compareProducts[$productName]);
            $this->clickControl('link', 'remove_item');
            return true;
        }
        return false;
    }

    /**
     * Get Field Names
     * @param $names
     * @return array $arrayNames
     */
    protected function _getFieldNames($names)
    {
        $arrayNames = array();
        foreach ($names as $key => $value) {
            if ($value == null) {
                if ($key == 0) {
                    if ($names[$key + 1] != null) {
                        $arrayNames[$key] = 'product_name';
                    } else {
                        $arrayNames[$key] = 'remove';
                        $arrayNames[$key + 1] = 'product_name';
                    }
                } elseif ($key == count($names) - 1) {
                    $arrayNames[$key] = 'product_prices';
                }
            } else {
                $arrayNames[$key] = $value;
            }
        }
        return $arrayNames;
    }

    /**
     * @param $returnArray
     */
    protected function _formDataForVerifying($returnArray)
    {
        foreach ($returnArray as &$value) {
            if (isset($value['remove'])) {
                unset($value['remove']);
            }
            $value['product_name'] =
                trim(preg_replace('/' . preg_quote($value['product_prices']) . '/', '', $value['product_name']));
            $value['product_prices'] =
                trim(preg_replace('#(add to wishlist)|(add to cart)|(\n)#i', ' ', $value['product_prices']),
                    " \t\n\r\0\x0B");
            preg_match_all('#([a-z (\.)?]+: ([a-z \.]+: )?)?\$([\d]+(\.|,)[\d]+(\.[\d]+)?)|([\d]+)#i',
                $value['product_prices'], $prices);
            $value['product_prices'] = array_map('trim', $prices[0]);

            foreach ($value['product_prices'] as $keyPrice => $price) {
                $prices = array_map('trim', explode('$', $price));
                $priceType = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '_', $prices[0])), '_');
                if (!$priceType) {
                    $priceType = 'price';
                }
                $value['product_prices'][$priceType] = $prices[1];
                unset($value['product_prices'][$keyPrice]);
            }
            $include = '';
            foreach ($value['product_prices'] as $priceType => $priceValue) {
                if (preg_match('/_excl_tax/', $priceType)) {
                    $include = preg_replace('/_excl_tax/', '', $priceType);
                }
                if ($priceType == 'incl_tax' && $include) {
                    $value['product_prices'][$include . '_' . $priceType] = $priceValue;
                    unset($value['product_prices'][$priceType]);
                }
            }
        }
        return $returnArray;
    }

    /**
     * Get available product details from the Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @return array $productData Product details from Compare Products pop-up
     */
    public function getProductDetailsOnComparePage()
    {
        $data = array();
        $names = array();
        $table = $this->getControlElement('fieldset', 'compare_products');
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $cellData
         */
        $nameCells = $this->getChildElements($table, '//th');
        $productCount = count($this->getChildElements($table, 'tbody[1]/tr/*')) - 1;
        foreach ($nameCells as $cellData) {
            $names[] = trim($cellData->text());
        }
        for ($i = 1; $i <= $productCount; $i++) {
            $columnData = $this->getChildElements($table, "//td[$i]");
            foreach ($columnData as $cellData) {
                $data[$i][] = $cellData->text();
            }

        }
        $arrayNames = $this->_getFieldNames($names);

        //Generate correct array
        $returnArray = array();
        foreach ($data as $number => $productData) {
            foreach ($productData as $key => $value) {
                $returnArray['product_' . ($number)][$arrayNames[$key]] = $value;
            }
            unset($data[$number]);
        }

        return $this->_formDataForVerifying($returnArray);
    }

    /**
     * Compare provided products data with actual info in Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened and selected
     *
     * @param array $verifyData Array of products info to be checked
     *
     * @return array Array of  error messages if any
     */
    public function frontVerifyProductDataInComparePopup($verifyData)
    {
        $actualData = $this->getProductDetailsOnComparePage();
        $this->assertEquals($verifyData, $actualData);
    }

    /**
     * Get list of available product attributes in Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @return array $attributesList Array of available product attributes in Compare Products pop-up
     *
     */
    public function frontGetAttributesListComparePopup()
    {
        $totalElements = $this->getControlCount('pageelement', 'product_attribute_names');
        $attributesList = array();
        for ($i = 1; $i < $totalElements + 1; $i++) {
            $this->addParameter('', $i);
            $elementValue = $this->getControlAttribute('pageelement', 'product_attribute_index_name', 'text');
            $attributesList[$elementValue] = $i;
        }
        return $attributesList;
    }

    /**
     * Get list of available products in Compare Products pop-up
     * Preconditions: Compare Products pop-up is opened
     *
     * @return array
     */
    public function frontGetProductsListComparePopup()
    {
        $totalElements = $this->getControlCount('pageelement', 'product_names');
        $productsList = array();
        for ($i = 1; $i < $totalElements + 1; $i++) {
            $this->addParameter('', $i);
            $elementValue = $this->getControlAttribute('pageelement', 'product_index_name', 'text');
            $productsList[$elementValue] = $i;
        }
        return $productsList;
    }

    /**
     * Open ComparePopup And set focus
     *
     * Preconditions: Page with Compare block is opened
     *
     * @return string Pop-up ID
     */
    public function frontOpenComparePopup()
    {
        $this->clickButton('compare', false);
        $popupId = $this->selectLastWindow();
        $this->validatePage('compare_products');
        return $popupId;
    }

    /**
     * Close ComparePopup and set focus to main window
     *
     * Preconditions: ComparePopup is opened
     *
     * @param string $popupId
     */
    public function frontCloseComparePopup($popupId)
    {
        if (!$popupId) {
            return;
        }
        $this->closeWindow($popupId);
        //Select parent window
        $this->window('');
    }
}