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
 * Class MultishippingGuestPaypalDirect
 * PayPal Payments Pro Method
 * Register on checkout to checkout with multi shipping using PayPal Payments Pro payment method
 *
 * @ZephyrId MAGETWO-12836
 * @package Magento\Checkout\Test\Fixture
 */
class MultishippingGuestPaypalDirect extends Checkout
{
    /**
     * Data for guest multishipping checkout with Payments Pro Method
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => array(
                    '$15.83', //simple
                    '$16.92' //configurable
                )
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
            'display_price',
            'display_shopping_cart',
            'default_tax_config'
        ));
        //Tax
        Factory::getApp()->magentoTaxRemoveTaxRule();
        $taxRule = Factory::getFixtureFactory()->getMagentoTaxTaxRule();
        $taxRule->switchData('custom_rule');
        $taxRule->persist();
        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple->switchData('simple_required');
        $simple->persist();

        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->switchData('configurable_required');
        $configurable->persist();

        $this->products = array(
            $simple,
            $configurable
        );
        //Checkout data
        $this->customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customer->switchData('customer_US_1');
        $address1 = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $address1->switchData('address_US_1');
        $address2 = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $address2->switchData('address_US_2');
        $this->shippingAddresses = array(
            $address1,
            $address2
        );

        $newShippingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $newShippingAddress->switchData('address_US_2');
        $this->newShippingAddresses = array($newShippingAddress);

        $shippingMethod1 = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $shippingMethod1->switchData('flat_rate');
        $shippingMethod2 = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $shippingMethod2->switchData('flat_rate');
        $this->shippingMethods = array(
            $shippingMethod1,
            $shippingMethod2
        );
        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('paypal_direct');
        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_direct');
        $this->bindings = array(
            $simple->getProductName() => $address1->getOneLineAddress(),
            $configurable->getProductName() => $address2->getOneLineAddress()
        );
    }
}