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
 * Class TaxRate
 *
 * @package Magento\Tax\Test\Fixture
 */
class TaxRate extends DataFixture
{
    /**
     * Get tax rate name
     *
     * @return string
     */
    public function getTaxRateName()
    {
        return $this->getData('fields/code/value');
    }

    /**
     * Create tax rate
     *
     * @return TaxRate
     */
    public function persist()
    {
        Factory::getApp()->magentoTaxCreateTaxRate($this);

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
                    'value' => 'Tax Rate %isolation%'
                ),
                'rate' => array(
                    'value' => '10'
                ),
                'tax_country_id' => array(
                    'value' => 'US',
                ),
                'tax_postcode' => array(
                    'value' => '*'
                ),
                'tax_region_id' => array(
                    'value' => '0'
                )
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoTaxTaxRate($this->_dataConfig, $this->_data);
    }
}
