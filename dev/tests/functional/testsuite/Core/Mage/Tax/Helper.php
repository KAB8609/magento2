<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
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
class Core_Mage_Tax_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Define Store View id in Table by name
     *
     * @param string $storeView
     *
     * @return integer
     */
    public function findTaxTitleByName($storeView)
    {
        $taxTitleQty = $this->getControlCount('pageelement', 'tax_title_header');
        for ($i = 1; $i <= $taxTitleQty; $i++) {
            $this->addParameter('index', $i);
            $text = $this->getControlAttribute('pageelement', 'tax_title_header_index', 'text');
            if ($text == $storeView) {
                return $i;
            }
        }
        return 0;
    }

    /**
     * Create Product Tax Class|Customer Tax Class|Tax Rate|Tax Rule
     *
     * @param array|string $taxItemData
     * @param string $type search type rate|rule|customer_class|product_class
     */
    public function createTaxItem($taxItemData, $type)
    {
        if (is_string($taxItemData)) {
            $elements = explode('/', $taxItemData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $taxItemData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $this->clickButton('add_' . $type);
        $this->fillForm($taxItemData);

        $rateTitles = (isset($taxItemData['tax_titles'])) ? $taxItemData['tax_titles'] : array();
        if ($rateTitles && $type == 'rate') {
            $this->assertTrue($this->controlIsPresent('fieldset', 'tax_titles'),
                'Tax Titles for store views are defined, but cannot be set.');
            foreach ($rateTitles as $key => $value) {
                $this->addParameter('storeNumber', $this->findTaxTitleByName($key));
                $this->fillField('tax_title', $value);
            }
        }
        $this->saveForm('save_' . $type);
    }

    /**
     * Open Product Tax Class|Customer Tax Class|Tax Rate|Tax Rule
     *
     * @param array $taxSearchData Data for search
     * @param string $type search type rate|rule|customer_class|product_class
     *
     * @throws OutOfRangeException
     */
    public function openTaxItem(array $taxSearchData, $type)
    {
        $xpathTR = $this->search($taxSearchData, 'manage_tax_' . $type);
        $this->assertNotNull($xpathTR, 'Search item is not found');
        $url = $this->getElement($xpathTR)->attribute('title');
        switch ($type) {
            case 'rate':
                $cellId = $this->getColumnIdByName('Name');
                $this->addParameter($type, $this->defineParameterFromUrl($type, $url));
                break;
            case 'rule':
                $cellId = $this->getColumnIdByName('Tax Identifier');
                $this->addParameter($type, $this->defineParameterFromUrl($type, $url));
                break;
            case 'customer_class':
            case 'product_class':
                $cellId = $this->getColumnIdByName('class Core_Mage_Name');
                $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
                break;
            default:
                throw new OutOfRangeException('Unsupported value for parameter $type');
                break;
        }
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        $this->addParameter('elementTitle', $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text'));
        $this->clickControl('pageelement', 'table_line_cell_index');
    }

    /**
     * Delete Product Tax Class|Customer Tax Class|Tax Rate|Tax Rule
     *
     * @param array $taxSearchData Data for search
     * @param string $type search type rate|rule|customer_class|product_class
     */
    public function deleteTaxItem(array $taxSearchData, $type)
    {
        $this->openTaxItem($taxSearchData, $type);
        $this->clickButtonAndConfirm('delete_' . $type, 'confirmation_for_delete');
    }

    /**
     * Delete all Tax Rules except specified in $excludeList
     *
     * @param array $excludeList
     */
    public function deleteRulesExceptSpecified(array $excludeList)
    {
        $rules = array();
        $columnId = $this->getColumnIdByName('Name');
        $elements = $this->getControlElements('pageelement', 'rule_line');
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $element
         */
        foreach ($elements as $element) {
            $name = trim($this->getChildElement($element, "td[$columnId]")->text());
            if (!in_array($name, $excludeList)) {
                $rules[] = $name;
            }
        }
        foreach ($rules as $rule) {
            $this->deleteTaxItem(array('filter_name' => $rule), 'rule');
        }
    }
}