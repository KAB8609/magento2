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

namespace Magento\Checkout\Test\Fixture;

use Mtf\Factory\Factory;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class GuestPaypalDirect
 * PayPal Payments Pro Method
 * Guest checkout using PayPal Payments Pro method and offline shipping method
 *
 * @ZephyrId MAGETWO-12968
 * @package Magento\Checkout\Test\Fixture
 */
class GuestPaypalDirect extends Checkout
{
    /**
     * Prepare data for guest checkout with PayPal Payments Pro Method
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$166.72'
            )
        );
    }

    /**
     * Setup fixture
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration(array(
            'flat_rate',
            'paypal_disabled_all_methods',
            'paypal_direct',
            'default_tax_config',
            'display_price',
            'display_shopping_cart',
            'default_tax_config'
        ));
        //Tax
        $taxRule = Factory::getFixtureFactory()->getMagentoTaxTaxRule();
        $taxRule->switchData('custom_rule');
        $taxRule->persist();
        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple->switchData('simple');
        $bundle = Factory::getFixtureFactory()->getMagentoBundleBundle();
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->switchData('configurable_default_category');

        $simple->persist();
        $bundle->persist();
        $configurable->persist();
        $this->products = array(
            $simple,
            $bundle,
            $configurable
        );
        //Checkout data
        $this->billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $this->billingAddress->switchData('address_US_3');

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('flat_rate');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('paypal_direct');

        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_direct');
    }
}
