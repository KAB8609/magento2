<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_TermsAndConditions
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
class Core_Mage_TermsAndConditions_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create a Terms and Conditions
     *
     * @param string|array $termsData
     */
    public function createTermsAndConditions($termsData)
    {
        if (is_string($termsData)) {
            $elements = explode('/', $termsData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $termsData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $this->clickButton('create_new_terms_and_conditions');
        if (array_key_exists('store_view', $termsData) && !$this->controlIsPresent('multiselect', 'store_view')) {
            unset($termsData['store_view']);
        }
        $this->fillFieldset($termsData, 'sales_new_condition');
        $this->saveForm('save_condition');
    }

    /**
     * Opens Terms and Conditions
     *
     * @param array $searchTerms
     */
    public function openTermsAndConditions(array $searchTerms)
    {
        if (array_key_exists('filter_store_view', $searchTerms)
            && !$this->controlIsPresent('dropdown', 'filter_store_view')
        ) {
            unset($searchTerms['filter_store_view']);
        }
        $xpathTR = $this->search($searchTerms, 'sales_checkout_terms_and_conditions_grid');
        $this->assertNotEquals(null, $xpathTR, 'Terms is not found');
        $termId = $this->getColumnIdByName('Condition Name');
        $param = trim($this->getElement($xpathTR . '//td[' . $termId . ']')->text());
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->getElement($xpathTR)->click();
        $this->waitForPageToLoad();
        $this->validatePage();
    }

    /**
     * Deletes Terms and Conditions
     *
     * @param array $searchTerms
     */
    public function deleteTerms(array $searchTerms)
    {
        $this->openTermsAndConditions($searchTerms);
        $this->clickButtonAndConfirm('delete_condition', 'confirmation_for_delete');
    }

    /**
     * Deletes All Terms and Conditions in the Grid
     */
    public function deleteAllTerms()
    {
        $termId = $this->getColumnIdByName('Condition Name');
        while (!$this->controlIsPresent('message', 'no_records_found')) {
            $this->addParameter('columnId', $termId);
            $this->addParameter('elementTitle',
                $this->getControlAttribute('pageelement', 'select_terms_in_grid', 'text'));
            $this->clickControl('pageelement', 'select_terms_in_grid');
            $this->clickButtonAndConfirm('delete_condition', 'confirmation_for_delete');
            $this->assertMessagePresent('success', 'condition_deleted');
        }
    }

    /**
     * Verify entered data
     *
     * @param string $termsData
     */
    public function verifyTermsAndConditions($termsData)
    {
        if (is_string($termsData)) {
            $elements = explode('/', $termsData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $termsData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        if (array_key_exists('store_view', $termsData) && !$this->controlIsPresent('multiselect', 'store_view')) {
            unset($termsData['store_view']);
        }
        $this->verifyForm($termsData);
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Get Terms and Conditions ID for checkout
     *
     * @param array $searchData
     *
     * @return string $id
     */
    public function getAgreementId($searchData)
    {
        $xpathTR = $this->search($searchData, 'sales_checkout_terms_and_conditions_grid');
        $this->assertNotNull($xpathTR, 'Terms is not found');
        return trim($this->getElement($xpathTR . '//td[' . $this->getColumnIdByName('ID') . ']')->text());
    }
}