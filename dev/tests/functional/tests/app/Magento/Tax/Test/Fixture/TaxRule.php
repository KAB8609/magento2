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

namespace Magento\Tax\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class TaxRule
 *
 * @package Magento\Tax\Test\Fixture
 */
class TaxRule extends DataFixture
{
    /**
     * Get tax rule name
     *
     * @return string
     */
    public function getTaxRuleName()
    {
        return $this->getData('fields/code/value');
    }

    /**
     * Create tax rule
     *
     * @return TaxRule
     */
    public function persist()
    {
        Factory::getApp()->magentoTaxCreateTaxRule($this);

        return $this;
    }

    /**
     * Init data
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'code' => array(
                    'value' => 'Tax Rule %isolation%'
                ),
                'tax_rate' => array(
                    'value' => '1',
                    'curl' => 'tax_rate[]'
                ),
                'tax_product_class' => array(
                    'value' => '2',
                    'curl' => 'tax_product_class[]'
                ),
                'tax_customer_class' => array(
                    'value' => '3',
                    'curl' => 'tax_customer_class[]'
                ),
                'priority' => array(
                    'value' => '0'
                ),
                'position' => array(
                    'value' => '0'
                )
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoTaxTaxRule($this->_dataConfig, $this->_data);
    }
}
