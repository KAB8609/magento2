<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CheckoutOnePage
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page Checkout test for different products
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutOnePage_Guest_WithProductsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Creating Simple and Virtual products</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible');
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'simple' => $simple['general_name'],
            'virtual' => $virtual['general_name']
        );
    }

    /**
     * <p>Checkout with simple product.</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3193
     */
    public function withSimpleProduct($data)
    {
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney_usa',
            array('general_name' => $data['simple']));
        //Steps
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Checkout with virtual product.</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3194
     */
    public function withVirtualProduct($data)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney_virtual',
            array('general_name' => $data['virtual']));
        //Steps
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }
}