<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Block\Adminhtml\Rule\Edit;

use Mtf\Fixture;
use Mtf\Client\Element\Locator;
use Mtf\Block\Form as FormInterface;

/**
 * Class TaxRate
 * Tax rate block
 *
 * @package Magento\Tax\Test\Block\Adminhtml\Rule\Edit
 */
class TaxRate extends FormInterface
{
    /**
     * 'Add New Tax Rate' button
     *
     * @var string
     */
    private $addNewTaxRate;

    /**
     * Dialog window for creating new tax rate
     *
     * @var string
     */
    private $taxRateUiDialog;

    /**
     * 'Save' button on dialog window for creating new tax rate
     *
     * @var string
     */
    private $saveTaxRate;

    /**
     * Tax rate option
     *
     * @var string
     */
    private $taxRateOption;

    /**
     * Initialize elements in block
     */
    protected function _init()
    {
        $this->addNewTaxRate = '.action-add';
        $this->taxRateUiDialog = '//*[contains(@class, ui-dialog)]//*[@id="tax-rate-form"]/..';
        $this->saveTaxRate = '#tax-rule-edit-apply-button';
        $this->taxRateOption = '//*[contains(@class, "mselect-list-item")]//label';
    }

    /**
     * Select Tax Rate in multiselect and create new one if required
     *
     * @param array $rates
     */
    public function selectTaxRate(array $rates)
    {
        foreach ($rates as $rate) {
            if (isset($rate['rate'])) {
                $this->_rootElement->find($this->addNewTaxRate, Locator::SELECTOR_CSS)->click();
                $taxRateDialog = $this->_rootElement->find($this->taxRateUiDialog, Locator::SELECTOR_XPATH);
                $this->_fill($this->dataMapping($rate), $taxRateDialog);
                $taxRateDialog->find($this->saveTaxRate, Locator::SELECTOR_CSS)->click();
                $this->waitForElementNotVisible($this->taxRateUiDialog, Locator::SELECTOR_XPATH);
            } else {
                $this->_rootElement->find($this->taxRateOption . '/span[text()="' . $rate['code']['value'] . '"]',
                    Locator::SELECTOR_XPATH)->click();
            }
        }
    }
}