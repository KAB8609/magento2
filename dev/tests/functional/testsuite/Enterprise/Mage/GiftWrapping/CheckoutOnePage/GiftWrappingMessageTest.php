<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftWrapping
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Tests Gift Wrapping.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_CheckoutOnePage_GiftWrappingMessageTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_use_default_per_website');
    }

    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        //Load default application settings
        $this->getConfigHelper()->getConfigAreas(true);
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_use_default_per_website');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Create Staging Website</p>
     *
     * @return array $website
     * @test
     */
    public function createWebsite()
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('StagingWebsite/staging_website_enable_auto_entries');
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('success', 'success_created_website');

        return $website;
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return array $productData
     * @test
     */
    public function preconditionsCreateProduct()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @param array $website
     *
     * @return array $productData
     * @test
     * @depends createWebsite
     */
    public function preconditionsCreateProductForWebsite($website)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible',
            array('websites' => $website['general_information']['staging_website_name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Create Customer</p>
     *
     * @test
     * @return array $userData
     */
    public function preconditionsCreateCustomer()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verification
        $this->assertMessagePresent('success', 'success_registration');
        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Create Customer</p>
     *
     * @param array $website
     *
     * @return array $userData
     * @test
     * @depends createWebsite
     */
    public function preconditionsCreateCustomerForWebsite($website)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $newFrontendUrl =
            $this->stagingWebsiteHelper()->buildFrontendUrl($website['general_information']['staging_website_code']);
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verification
        $this->assertMessagePresent('success', 'success_registration');
        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Create GiftWrapping without image</p>
     *
     * @return array $giftWrappingData
     * @test
     */
    public function preconditionsGiftWrapping()
    {
        //Data
        $giftWrappingDataWithoutImg = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $giftWrappingDataWithImg = $this->loadDataSet('GiftWrapping', 'gift_wrapping_with_image');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingDataWithoutImg);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        //Steps
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingDataWithImg);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        return array('img' => $giftWrappingDataWithImg, 'noImg' => $giftWrappingDataWithoutImg);
    }

    /**
     * @TestlinkId TL-MAGE-850
     * <p>Gift Message for entire Order is allowed</p>
     * <p>Verify that when "Allow Gift Messages on Order Level" setting is set to "Yes",
     * customer has an ability to add Gift Message to entire Order during OnePageCheckout,
     * prompt for which should be present on Shipping Method step after checking the appropriate checkbox</p>
     *
     * @param $productData
     * @param $userData
     *
     * @test
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     */
    public function giftMessageForEntireOrder($productData, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_for_order_enable');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('add_gift_options' => $this->loadDataSet('OnePageCheckout',
                'gift_message_with_gift_wrapping_one_page')), array('product_1' => $productData['general_name']));
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('elementTitle', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId), 'sales_order_grid');
        $this->orderHelper()->verifyGiftMessage($checkoutData['shipping_data']['add_gift_options']);
    }

    /**
     * @TestlinkId TL-MAGE-881
     * @TestlinkId TL-MAGE-905
     * <p>Gift Message for entire Order is not allowed (message-no; wrapping-no)</p>
     * <p>Gift Message for Individual Items is not allowed (message-no; wrapping-no)</p>
     * <p>Verify that when "Allow Gift Messages on Order Level" setting is set to "No",
     * customer is not able to add Gift Message to entire Order during OnePageCheckout</p>
     *
     * @param $productData
     * @param $userData
     * @param $entity
     *
     * @test
     * @dataProvider giftMessageIsNotAllowedWrappingNoDataProvider
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     */
    public function giftMessageIsNotAllowedWrappingNo($entity, $productData, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        //        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_' . $entity . '_disable');
        //        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_' . $entity . '_disable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_printed_card_enable');
        $entity = substr($entity, 4, strlen($entity));
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $productData['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        //Verification
        $this->addParameter('productName', $productData['general_name']);
        if ($this->controlIsPresent('checkbox', 'gift_option_for_' . $entity)) {
            $this->assertFalse($this->controlIsVisible('checkbox', 'gift_option_for_' . $entity),
                '"Add gift options" checkbox is visible ' . $entity);
        }
        if ($this->controlIsPresent('link', $entity . '_gift_message')) {
            $this->assertFalse($this->controlIsVisible('link', $entity . '_gift_message'),
                '"Gift Message" link is visible' . $entity);
        }
    }

    public function giftMessageIsNotAllowedWrappingNoDataProvider()
    {
        return array(
            array('per_item'),
            array('for_order')
        );
    }

    /**
     * @TestlinkId TL-MAGE-891
     * @TestlinkId TL-MAGE-906
     * <p>Gift Message for entire Order is not allowed (message-no; wrapping-yes)</p>
     * <p>Gift Message for Individual Items is not allowed (message-no; wrapping-yes)</p>
     * <p>Verify that when "Allow Gift Messages on Order Level" setting is set to "No",
     * customer is not able to add Gift Message to entire Order during OnePageCheckout</p>
     *
     * @param $productData
     * @param $userData
     * @param $entity
     *
     * @test
     * @dataProvider giftMessageIsNotAllowedWrappingYesDataProvider
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     */
    public function giftMessageIsNotAllowedWrappingYes($entity, $productData, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        //        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_' . $entity . '_disable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_' . $entity . '_enable');
        $entity = substr($entity, 4, strlen($entity));
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $productData['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        $this->fillCheckbox('gift_option_for_order', 'Yes');
        //Verification
        $this->addParameter('productName', $productData['general_name']);
        if ($this->controlIsPresent('link', $entity . '_gift_message')) {
            $this->assertFalse($this->controlIsVisible('link', $entity . '_gift_message'),
                '"Gift Message" link is visible' . $entity);
        }
    }

    public function giftMessageIsNotAllowedWrappingYesDataProvider()
    {
        return array(
            array('per_item'),
            array('for_order')
        );
    }

    /**
     * @TestlinkId TL-MAGE-900
     * <p>Gift Message for Individual Items is allowed</p>
     * <p>Verify that when "Allow Gift Messages for Order Items" setting is set to "Yes",
     * customer has an ability to add Gift Message to Individual Items during OnePageCheckout,
     * prompt for which should be present on Shipping Method step after checking the appropriate checkbox</p>
     *
     * @param $productData
     * @param $userData
     *
     * @test
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     */
    public function giftMessageForIndividualItemsIsAllowed($productData, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_per_item_enable');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('individual_items' => $this->loadDataSet('OnePageCheckout',
                'gift_message_for_individual_items_one_page', null,
                array('product_1' => $productData['general_name']))),
            array('product_1' => $productData['general_name']));
        $giftMsg = $checkoutData['shipping_data']['add_gift_options']['individual_items']['item_1'];
        $vrfGiftData = $this->loadDataSet('OnePageCheckout', 'verify_gift_data',
            array('sku_product' => $productData['general_sku'],
                  'from'        => $giftMsg['gift_message']['item_gift_message_from'],
                  'to'          => $giftMsg['gift_message']['item_gift_message_to'],
                  'message'     => $giftMsg['gift_message']['item_gift_message']));
        $vrfGiftData = $this->clearDataArray($vrfGiftData);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('elementTitle', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId), 'sales_order_grid');
        $this->orderHelper()->verifyGiftMessage($vrfGiftData);
    }

    /**
     * @TestlinkId TL-MAGE-898
     * @TestlinkId TL-MAGE-910
     * <p>Gift Wrapping image displaying for Entire Order gift options (image is not specified)</p>
     * <p>Gift Wrapping image displaying for Individual Items gift options (image is not specified)</p>
     * <p>Test Case TL-MAGE-842: Enabling Gift Wrapping (OnePageCheckout)</p>
     * <p>Verify that when setting "Allow Gift Wrapping on Order Level" is set to "Yes",
     * customer has ability to select Gift Wrapping (with not specified picture which will not be displayed)
     * for entire Order using dropdown "Gift Wrapping Design" on Payment Method step of OnePageCheckout</p>
     *
     * @param $productData
     * @param $userData
     * @param $giftWrappingData
     * @param $entity
     *
     * @test
     * @dataProvider giftWrappingImageDisplayingGiftOptionsDataProvider
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @depends preconditionsGiftWrapping
     */
    public function giftWrappingImageDisplayingGiftOptions($entity, $productData, $userData, $giftWrappingData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_' . $entity . '_enable');
        $entity = substr($entity, 4, strlen($entity));
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $productData['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillForm(array('add_gift_options'                => 'Yes', 'gift_option_for_' . $entity => 'Yes',
                              $entity . '_gift_wrapping_design' => $giftWrappingData['noImg']['gift_wrapping_design']));
        //Verification
        $this->addParameter('itemName', $productData['general_name']);
        $this->assertTrue($this->controlIsPresent('pageelement', $entity . '_gift_wrapping_price'),
            'There is no price for "Gift Wrapping Design"');
        $this->assertFalse($this->controlIsPresent('pageelement', $entity . '_gift_wrapping_image'),
            'Picture for Gift wrapping is displayed');
    }

    public function giftWrappingImageDisplayingGiftOptionsDataProvider()
    {
        return array(
            array('per_item'),
            array('for_order')
        );
    }

    /**
     * @TestlinkId TL-MAGE-924
     * <p>Printed Card is allowed </p>
     * <p>Verify that when "Allow Printed Card" setting is set to "Yes",customer in process of OnePageCheckout
     * have ability to add Printed Card to Order after checking the appropriate checkbox</p>
     *
     * @param $productData
     * @param $userData
     *
     * @test
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     */
    public function printedCardIsAllowed($productData, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $printedCardOptions = $this->loadDataSet('GiftMessage', 'gift_printed_card_enable');
        $expectedPrintedCardPrice =
            $printedCardOptions['tab_1']['configuration']['gift_options']['default_price_for_printed_card'];
        $this->systemConfigurationHelper()->configure($printedCardOptions);
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('add_gift_options' => $this->loadDataSet('OnePageCheckout', 'gift_message_gift_printed_card')),
            array('product_1' => $productData['general_name']));
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('elementTitle', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId), 'sales_order_grid');
        $priceElement = $this->getElement($this->_getControlXpath('pageelement', 'printed_card_price'));
        $printedCardPrice = trim($priceElement->text(), '$\t\n\r');
        $this->assertEquals($expectedPrintedCardPrice, $printedCardPrice,
            "Printed Card price is different. Actual: $printedCardPrice. Expected:  . $expectedPrintedCardPrice");
    }

    /**
     * @TestlinkId TL-MAGE-998
     * <p>Printed Card ia not allowed</p>
     * <p>Verify that when "Allow Printed Card" setting is set to "No",
     * customer is not able to add Printed Card to Order during OnePageCheckout.</p>
     *
     * @param $productData
     * @param $userData
     *
     * @test
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     */
    public function printedCardIsNotAllowed($productData, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_enable');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $productData['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        //Verification
        if ($this->controlIsPresent('checkbox', 'add_printed_card')) {
            $this->assertFalse($this->controlIsVisible('checkbox', 'add_printed_card'),
                '"Add Printed card" checkbox is visible');
        }
    }

    /**
     * @TestlinkId TL-MAGE-996
     * <p>Gift Receipt is allowed</p>
     * <p>Verify that when "Allow Gift Receipt" setting is set to "Yes",
     * customer has an ability to enable this option to each order during OnePageCheckout.</p>
     *
     * @param $productData
     * @param $userData
     *
     * @test
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     */
    public function giftReceiptIsAllowed($productData, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_enable');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('add_gift_options' => $this->loadDataSet('OnePageCheckout', 'gift_message_gift_receipt')),
            array('product_1' => $productData['general_name']));
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('elementTitle', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId), 'sales_order_grid');
        $this->orderHelper()->verifyGiftMessage(array('entire_order' => array('order_send_gift_receipt' => 'Yes')));
    }


    /**
     * @TestlinkId TL-MAGE-997
     * <p>Gift Receipt is not allowed</p>
     * <p>Verify that when "Allow Gift Receipt" setting is set to "No",
     * customer is not able to enable this option to each order during OnePageCheckout.</p>
     *
     * @param $productData
     * @param $userData
     *
     * @test
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     */
    public function giftReceiptIsNotAllowed($productData, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_printed_card_enable');
        //        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_disable');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $productData['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        //Verification
        if ($this->controlIsPresent('checkbox', 'send_gift_receipt')) {
            $this->assertFalse($this->controlIsVisible('checkbox', 'send_gift_receipt'),
                '"Send Gift Receipt" checkbox is visible');
        }
    }

    /**
     * @TestlinkId TL-MAGE-912
     * @TestlinkId TL-MAGE-918
     * <p>Recounting Gift Options (Entire Order)</p>
     * <p>Recounting Gift Options (Individual Item)</p>
     * <p>Verify that customer can change configuration of Gift Options more than one time during OnePageCheckout,
     * and Gift Options prices/Grand Total on Order Review step will be recounted according to these changes.</p>
     *
     * @param $entity
     * @param $productData
     * @param $userData
     * @param $giftWrappingData
     *
     * @test
     * @dataProvider recountingGiftOptionsDataProvider
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     * @depends preconditionsGiftWrapping
     */
    public function recountingGiftOptions($entity, $productData, $userData, $giftWrappingData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_for_order_enable');
        //Data
        if ($entity == 'items') {
            $giftWrapping = $this->loadDataSet('OnePageCheckout', 'items_gift_wrapping', null,
                array('product_1'            => $productData['general_name'],
                      'gift_wrapping_design' => $giftWrappingData['noImg']['gift_wrapping_design']));
        } else {
            $giftWrapping = $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping', null,
                array('gift_wrapping_design' => $giftWrappingData['noImg']['gift_wrapping_design']));
        }
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_no_img_one_page',
            array('add_gift_options'             => $giftWrapping,
                  'gift_wrapping_for_' . $entity => '$' . $giftWrappingData['noImg']['gift_wrapping_price']),
            array('product_1' => $productData['general_name'], 'validate_product_1' => $productData['general_name']));
        $checkoutData = $this->clearDataArray($checkoutData);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->checkoutOnePageHelper()->frontOrderReview($checkoutData);
        $this->clickControl('link', 'shipping_method_change_link', false);
        //Data
        if ($entity == 'items') {
            $giftWrapping = $this->loadDataSet('OnePageCheckout', 'items_gift_wrapping', null,
                array('product_1'            => $productData['general_name'],
                      'gift_wrapping_design' => $giftWrappingData['img']['gift_wrapping_design']));
        } else {
            $giftWrapping = $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping', null,
                array('gift_wrapping_design' => $giftWrappingData['img']['gift_wrapping_design']));
        }
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_with_img_one_page',
            array('add_gift_options'             => $giftWrapping,
                  'gift_wrapping_for_' . $entity => '$' . $giftWrappingData['img']['gift_wrapping_price']),
            array('product_1' => $productData['general_name'], 'validate_product_1' => $productData['general_name']));

        $checkoutData = $this->clearDataArray($checkoutData);
        //Steps
        $this->checkoutOnePageHelper()->frontSelectShippingMethod($checkoutData['shipping_data']);
        $this->checkoutOnePageHelper()->frontSelectPaymentMethod($checkoutData['payment_data']);
        $this->checkoutOnePageHelper()->frontOrderReview($checkoutData);
        $this->clickControl('link', 'shipping_method_change_link', false);
        $this->validatePage();
        //Data
        if ($entity == 'items') {
            $giftWrapping = $this->loadDataSet('OnePageCheckout', 'items_gift_wrapping', null,
                array('product_1' => $productData['general_name'], 'gift_wrapping_design' => 'Please select'));
        } else {
            $giftWrapping = $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping', null,
                array('gift_wrapping_design' => 'Please select'));
        }
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'recount_no_gift_wrapping_one_page',
            array('add_gift_options' => $giftWrapping),
            array('product_1' => $productData['general_name'], 'validate_product_1' => $productData['general_name']));
        //Steps
        $this->checkoutOnePageHelper()->frontSelectShippingMethod($checkoutData['shipping_data']);
        $this->checkoutOnePageHelper()->frontSelectPaymentMethod($checkoutData['payment_data']);
        unset($checkoutData['shipping_data']['add_gift_options']);
        $this->checkoutOnePageHelper()->frontOrderReview($checkoutData);
    }


    public function recountingGiftOptionsDataProvider()
    {
        return array(
            array('items'),
            array('order')
        );
    }

    /**
     * @TestlinkId TL-MAGE-920
     * <p>Recounting Gift Options (Printed Card to Order)</p>
     * <p>Verify that customer can change Printed Card configuration more than one time during OnePageCheckout,
     * and Printed Card prices/Grand Total on Order Review step are recounted according to these changes.</p>
     *
     * @param $productData
     * @param $userData
     *
     * @test
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     */
    public function recountingGiftOptionsPrintedCard($productData, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_printed_card_enable');
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_printed_card_yes_one_page', null,
            array('product_1' => $productData['general_name']));
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->checkoutOnePageHelper()->frontOrderReview($checkoutData);
        $this->clickControl('link', 'shipping_method_change_link', false);
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_printed_card_no_one_page', null,
            array('product_1' => $productData['general_name']));
        //Steps
        $this->checkoutOnePageHelper()->frontSelectShippingMethod($checkoutData['shipping_data']);
        $this->checkoutOnePageHelper()->frontSelectPaymentMethod($checkoutData['payment_data']);
        $this->checkoutOnePageHelper()->frontOrderReview($checkoutData);
    }

    /**
     * @TestlinkId TL-MAGE-1037
     * @TestlinkId TL-MAGE-847
     * <p>No Gift Wrappings is created</p>
     * <p>Disabling Gift Wrapping (OnePageCheckout)</p>
     * <p>Verify that when setting "Allow Gift Wrapping on Order Level" is set to "Yes" and setting
     * "Allow gift wrapping for Order Item" is set to "Yes", dropdown "Gift Wrapping Design" on Payment Method step of
     * checkout should be absent for Entire Order and Individual Item</p>
     *
     * @param $productData
     * @param $userData
     *
     * @test
     * @depends preconditionsCreateProduct
     * @depends preconditionsCreateCustomer
     */
    public function noGiftWrappingsIsCreated($productData, $userData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_for_order_enable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_per_item_enable');
        //Disabling gift wrapping for TL-MAGE-847
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->disableAllGiftWrapping();
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1' => $productData['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($userData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        $this->fillCheckbox('gift_option_for_order', 'Yes');
        //Verification
        $this->assertFalse($this->controlIsPresent('dropdown', 'order_gift_wrapping_design'),
            '"Gift Wrapping Design" is in place');
        //Steps
        $this->fillCheckbox('gift_option_for_item', 'Yes');
        $this->addParameter('itemName', $productData['general_name']);
        //Verification
        $this->assertFalse($this->controlIsPresent('dropdown', 'item_gift_wrapping_design'),
            '"Gift Wrapping Design" is in place');
    }

    /**
     * @TestlinkId TL-MAGE-857
     * <p>Test Case : Possibility to adding Gift attributes to Order during the process of OnePageCheckout - Website</p>
     *
     * @param array $customerData
     * @param array $productData
     * @param array $website
     *
     * @test
     * @depends preconditionsCreateCustomerForWebsite
     * @depends preconditionsCreateProductForWebsite
     * @depends createWebsite
     */
    public function checkoutWithGiftWrappingAndMessageWebsiteScope($customerData, $productData, $website)
    {
        //Preconditions
        $giftWrappingEnableWebsite = $this->loadDataSet('GiftMessage', 'gift_wrapping_all_enable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $giftMessagesEnableWebsite = $this->loadDataSet('GiftMessage', 'gift_message_all_enable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $this->navigate('system_configuration');
        //        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_all_disable');
        //        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_all_disable');
        $this->systemConfigurationHelper()->configure($giftWrappingEnableWebsite);
        $this->systemConfigurationHelper()->configure($giftMessagesEnableWebsite);

        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image',
            array('gift_wrapping_websites' => $website['general_information']['staging_website_name']));
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('individual_items' => $this->loadDataSet('OnePageCheckout',
                'gift_message_for_individual_items_one_page', null,
                array('product_1'            => $productData['general_name'],
                      'gift_wrapping_design' => $giftWrappingData['gift_wrapping_design']))),
            array('product_1' => $productData['general_name']));

        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $newFrontendUrl =
            $this->stagingWebsiteHelper()->buildFrontendUrl($website['general_information']['staging_website_code']);
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * @TestlinkId TL-MAGE-868
     * <p>Test Case: Possibility to adding Gift attributes to Order during the process of OnePageCheckout</p>
     * <p>Check If possible to add Gift Attributes to Order during the process of
     * OnePageCheckout (at Default Website) when all "gift settings" in default scope is set to 'yes" and in the website
     * scope all of this settings set to ""No"</p>
     *
     * @param array $customerData
     * @param array $productData
     * @param array $website
     *
     * @test
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @depends createWebsite
     */
    public function possibilityToAddGiftAttributesToOrder($customerData, $productData, $website)
    {
        //Preconditions
        $giftWrappingEnableWebsite = $this->loadDataSet('GiftMessage', 'gift_wrapping_all_disable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $giftMessagesEnableWebsite = $this->loadDataSet('GiftMessage', 'gift_message_all_disable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_all_enable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure($giftWrappingEnableWebsite);
        $this->systemConfigurationHelper()->configure($giftMessagesEnableWebsite);
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $individualItemsMsg = $this->loadDataSet('OnePageCheckout', 'gift_message_for_individual_items_one_page', null,
            array('product_1'            => $productData['general_name'],
                  'gift_wrapping_design' => $giftWrappingData['gift_wrapping_design']));
        $entireOrder = $this->loadDataSet('OnePageCheckout', 'gift_message_with_gift_wrapping_one_page', null,
            array('gift_wrapping_design' => $giftWrappingData['gift_wrapping_design']));
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('entire_order' => $entireOrder['entire_order'], 'individual_items'  => $individualItemsMsg),
            array('product_1'      => $productData['general_name']));
        $vrfGiftData = $this->loadDataSet('OnePageCheckout', 'verify_gift_data',
            array('sku_product'         => $productData['general_sku'],
                  'from'                => $individualItemsMsg['item_1']['gift_message']['item_gift_message_from'],
                  'to'                  => $individualItemsMsg['item_1']['gift_message']['item_gift_message_to'],
                  'message'             => $individualItemsMsg['item_1']['gift_message']['item_gift_message'],
                  'from_order_level'    => $entireOrder['entire_order']['gift_message']['order_gift_message_from'],
                  'to_order_level'      => $entireOrder['entire_order']['gift_message']['order_gift_message_to'],
                  'message_order_level' => $entireOrder['entire_order']['gift_message']['order_gift_message'],));
        $vrfGiftWrapping = $this->loadDataSet('OnePageCheckout', 'verify_wrapping_data', null,
            array('gift_wrapping_design' => $giftWrappingData['gift_wrapping_design'],
                  'sku_product_1'        => $productData['general_sku'],
                  'price_order'          => '$' . $giftWrappingData['gift_wrapping_price'],
                  'price_product_1'      => '$' . $giftWrappingData['gift_wrapping_price']));
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->addParameter('elementTitle', '#' . $orderId);
        $this->searchAndOpen(array('filter_order_id' => $orderId), 'sales_order_grid');
        $this->orderHelper()->verifyGiftMessage($vrfGiftData);
        $this->orderHelper()->verifyGiftWrapping($vrfGiftWrapping);
    }

    /**
     * @TestlinkId TL-MAGE-869
     * <p>Test Case: Possibility to adding Gift attributes to Order during the process of OnePageCheckout - Global</p>
     * <p>Verify that it's possible to add Gift Attributes to Order during OnePageCheckout (at Default Website)
     * when all "gift settings" in default scope are set to "Yes"
     * and in the website scope all these settings are set to "No"</p>
     *
     * @param array $customerData
     * @param array $productData
     * @param array $website
     *
     * @test
     * @depends preconditionsCreateCustomerForWebsite
     * @depends preconditionsCreateProductForWebsite
     * @depends createWebsite
     */
    public function restrictionToAddGiftAttributesToOrder($customerData, $productData, $website)
    {
        //Preconditions
        $giftWrappingDisableWebsite = $this->loadDataSet('GiftMessage', 'gift_wrapping_all_disable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $giftMessagesDisableWebsite = $this->loadDataSet('GiftMessage', 'gift_message_all_disable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_all_enable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure($giftWrappingDisableWebsite);
        $this->systemConfigurationHelper()->configure($giftMessagesDisableWebsite);
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('product_1'      => $productData['general_name']));
        unset($checkoutData['shipping_data']);
        unset($checkoutData['payment_data']);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $newFrontendUrl =
            $this->stagingWebsiteHelper()->buildFrontendUrl($website['general_information']['staging_website_code']);
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->clickControl('link', 'back', false);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        //Verification
        if ($this->controlIsPresent('checkbox', 'add_gift_options')) {
            $this->assertFalse($this->controlIsVisible('checkbox', 'add_gift_options'),
                '"Add gift options" checkbox is visible');
        }
    }
}