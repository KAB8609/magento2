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
 * Tests for payment methods. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutOnePage_Guest_PaymentMethodsTest extends Mage_Selenium_TestCase
{

    protected static $useTearDown = false;

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->addParameter('id', '');
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadData('simple_product_for_order');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $simple['general_name'];
    }

    /**
     * <p>Payment methods without 3D secure.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Select Checkout Method as Guest</p>
     * <p>4. Fill in Billing Information tab.</p>
     * <p>5. Select "Ship to this address" option.</p>
     * <p>6. Click 'Continue' button.</p>
     * <p>7. Select Shipping Method.</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>9. Select Payment Method(by data provider).</p>
     * <p>10. Click 'Continue' button.</p>
     * <p>11. Verify information into "Order Review" tab</p>
     * <p>12. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @depends preconditionsForTests
     * @dataProvider dataWithout3DSecure
     * @test
     */
    public function differentPaymentMethodsWithout3D($payment, $simpleSku)
    {
        $checkoutData = $this->loadData('guest_flatrate_checkmoney',
                array('general_name' => $simpleSku, 'payment_data' => $this->loadData('front_payment_' . $payment)));
        if ($payment != 'checkmoney') {
            $payment .= '_without_3Dsecure';
        }
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($payment);
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    public function dataWithout3DSecure()
    {
        return array(
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('checkmoney'),
            array('payflowpro'),
            array('authorizenet')
        );
    }

    /**
     * <p>Payment methods with 3D secure.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Select Checkout Method as Guest</p>
     * <p>4. Fill in Billing Information tab.</p>
     * <p>5. Select "Ship to this address" option.</p>
     * <p>6. Click 'Continue' button.</p>
     * <p>7. Select Shipping Method.</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>9. Select Payment Method(by data provider).</p>
     * <p>10. Click 'Continue' button.</p>
     * <p>11. Enter 3D security code.</p>
     * <p>12. Verify information into "Order Review" tab</p>
     * <p>13. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @depends preconditionsForTests
     * @dataProvider dataWith3DSecure
     * @test
     */
    public function differentPaymentMethodsWith3D($payment, $simpleSku)
    {
        if ($payment == 'authorizenet') {
            self::$useTearDown = TRUE;
        }
        $checkoutData = $this->loadData('guest_flatrate_checkmoney',
                array('general_name' => $simpleSku, 'payment_data' => $this->loadData('front_payment_' . $payment)));
        //Steps
        $this->systemConfigurationHelper()->useHttps('frontend', 'yes');
        $this->systemConfigurationHelper()->configure($payment . '_with_3Dsecure');
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    public function dataWith3DSecure()
    {
        return array(
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('payflowpro'),
            array('authorizenet')
        );
    }

    protected function tearDown()
    {
        if (!empty(self::$useTearDown)) {
            $this->loginAdminUser();
            $this->systemConfigurationHelper()->useHttps('frontend', 'no');
        }
    }

}
