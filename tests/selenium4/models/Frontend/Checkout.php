<?php
/**
 * Frontend_checkout model
 *
 * @author Magento Inc.
 */
class Model_Frontend_Checkout extends Model_Frontend {
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->userData = Core::getEnvConfig('backend/user');
    }


    /**
     * Perform Checkout as a Guest from FrontEnd
     * @param - array wirh expecteded values:
     *       productUrl
     *       qty'firstName
     *       lastName
     *       company
     *       email
     *       street1
     *       street2
     *       city
     *       'country
     *       region
     *       postcode
     *       telephone
     *       fax
     */
    public function guestCheckout($params)
    {
        $this->printDebug('guestCheckout started...');
        //Open ProductPage, place one to ShoppingCart, Press 'Proceed to Checkout'
        $this->startCheckout($params);

        $this->setUiNamespace('frontend/pages/onePageCheckout/tabs/');

        //Select '...as Guest'
        $this->click($this->getUiElement('checkoutMethod/inputs/asGuest'));
        $this->click($this->getUiElement('checkoutMethod/buttons/continue'));

        // Fill billing address tab
        $this->fillBillingTab($params);

        $this->setUiNamespace('frontend/pages/onePageCheckout/tabs/');
        
        //Press Continue
        $this->click($this->getUiElement('billingAddress/buttons/continue'));
        $this->pleaseWaitStep($this->getUiElement('billingAddress/elements/pleaseWait'));

        //Perform rest of Checkout steps
        $this->shippingMethodPaymentPlaceOrderSteps($params);
        $this->printDebug('guestCheckout finished');
    }

/**
     * Perform Checkout with Registration from FrontEnd
     * @param - array wirh expecteded values:
     *       password
     *       productUrl
     *       qty"firstName
     *       lastName
     *       company
     *       email
     *       street1
     *       street2
     *       city
     *       "country
     *       region
     *       postcode
     *       telephone
     *       fax
     */
    public function registerCheckout($params)
    {
        //Open ProductPage, place one to ShoppingCart, Press "Proceed to Checkout"
        $this->startCheckout($params);

        $this->setUiNamespace('/frontend/pages/onePageCheckout/tabs/');

        //Select "...as Guest"
        $this->click($this->getUiElement("checkoutMethod/inputs/register"));
        $this->click($this->getUiElement("checkoutMethod/buttons/continue"));

        // Fill billing address tab
        $this->fillBillingTab($params);
        //Specify password with confirmation
        $this->setUiNamespace('/frontend/pages/onePageCheckout/tabs/');
        $this->type($this->getUiElement("billingAddress/inputs/password"),$params["password"]);
        $this->type($this->getUiElement("billingAddress/inputs/confirm"),$params["password"]);

        //Press Continue
        $this->click($this->getUiElement("billingAddress/buttons/continue"));
        $this->pleaseWaitStep($this->getUiElement("billingAddress/elements/pleaseWait"));
        $alert ='';

        if ($this->isAlertPresent()) {
                $this->storeAlert($alert);
                $this->printInfo("BillingInfo tab could not be saved. Customer email already exists.");
                //Use timestamp based value in email
                $this->type($this->getUiElement('billingAddress/inputs/email'),$this->getStamp() . '@varien.com');
                $this->type($this->getUiElement("billingAddress/inputs/password"),$params["password"]);
                $this->type($this->getUiElement("billingAddress/inputs/confirm"),$params["password"]);
                $this->click($this->getUiElement("billingAddress/buttons/continue"));
                $this->pleaseWaitStep($this->getUiElement("billingAddress/elements/pleaseWait"));
                $alert ='';

                if ($this->isAlertPresent()) {
                        $this->storeAlert($alert);
                        $this->setVerificationErrors("Check2: BillingInfo tab could not be saved. Customer email already exists ?");
                        return false;
                }
        }

        //Perform rest of Checkout steps
        $this->shippingMethodPaymentPlaceOrderSteps($params);
    }

    /**
     * Perform Checkout with login from FrontEnd
     * @param - array wirh expecteded values:
     *       password
     *       email
     */
    public function loginCheckout($params)
    {
        //Open ProductPage, place one to ShoppingCart, Press "Proceed to Checkout"
        $this->startCheckout($params);

        $this->setUiNamespace('/frontend/pages/onePageCheckout/tabs/');

        //Select '...as login'
        $this->type($this->getUiElement('checkoutMethod/inputs/loginEmail'),$params['email']);
        $this->type($this->getUiElement('checkoutMethod/inputs/password'),$params['password']);
        $this->clickAndWait($this->getUiElement('checkoutMethod/buttons/login'));
        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/messages/error'),1)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/error'));
            $this->setVerificationErrors("Login Error: " . $etext);
            return false;
        }

        $this->pleaseWaitStep($this->getUiElement('billingAddress/elements/pleaseWait'));

        // Fill billing address tab
        if ($this->waitForElement($this->getUiElement('billingAddress/elements/tabLoaded'), 5)) {
            $this->click($this->getUiElement('billingAddress/inputs/use_for_shipping'));
        };

        //Press Continue
        $this->click($this->getUiElement('billingAddress/buttons/continue'));

        //Perform rest of Checkout steps
        $this->shippingMethodPaymentPlaceOrderSteps($params);
    }

   /**
     * Perform Checkout witg Sign In from FrontEnd
     * @param - array wirh expecteded values:
     *       password
     *       productUrl
     *       qty"firstName
     *       lastName
     *       company
     *       email
     *       street1
     *       street2
     *       city
     *       "country
     *       region
     *       postcode
     *       telephone
     *       fax
     */
    public function multiShippingRegisterCheckout($params)
    {
        //Open ProductPage, place one to ShoppingCart, Press "Proceed to Checkout"
        $this->startCheckout($params, true);

        //Press 'Register'
        $this->clickAndWait($this->getUiElement('/frontend/pages/multiShippingCheckout/checkoutMethod/buttons/register'));

        $this->setUiNamespace('/frontend/pages/multiShippingCheckout/createAccount/');

        // Fill billing address tab
        $this->type($this->getUiElement('inputs/firstName'),$params['firstName']);
        $this->type($this->getUiElement('inputs/lastName'),$params['lastName']);
        $this->type($this->getUiElement('inputs/company'),$params['company']);
        $this->type($this->getUiElement('inputs/email'),$params['email']);
        $this->type($this->getUiElement('inputs/street1'),$params['street1']);
        $this->type($this->getUiElement('inputs/street2'),$params['street2']);
        $this->type($this->getUiElement('inputs/city'),$params['city']);
        $this->type($this->getUiElement('inputs/postcode'),$params['postcode']);
        $this->type($this->getUiElement('inputs/telephone'),$params['telephone']);
        //Country and Region
        $this->selectCountry($this->getUiElement('selectors/country'),$params['country']);
        $this->selectRegion($this->getUiElement('selectors/region'),$params['region']);

        //Specify password with confirmation
        $this->type($this->getUiElement('inputs/password'),$params['password']);
        $this->type($this->getUiElement('inputs/confirm'),$params['password']);

        //Press Submit
        $this->clickAndWait($this->getUiElement('buttons/submit'));

        if ($this->waitForElement($this->getUiElement('messages/alreadyExists'), 5)) {
                $this->printInfo('Account could not be created. Customer email already exists');
                $this->printInfo('Registering with new email');
                //Using stamp based email
                $this->type($this->getUiElement('inputs/email'),$this->getStamp() . '@varien.com');
                $this->type($this->getUiElement('inputs/password'),$params['password']);
                $this->type($this->getUiElement('inputs/confirm'),$params['password']);                
                $this->clickAndWait($this->getUiElement('buttons/submit'));
        } 
        // Add new Address
        $this->clickAndWait($this->getUiElement('/frontend/pages/multiShippingCheckout/tabs/selectAddresses/buttons/enterNewAddress'));
        // Fill new address fields
        $this->setUiNamespace('/frontend/pages/multiShippingCheckout/tabs/createShippingAddress/');
        $this->type($this->getUiElement('inputs/firstName'),$params['firstName'] . 'Second Address');
        $this->type($this->getUiElement('inputs/lastName'),$params['lastName']);
        $this->type($this->getUiElement('inputs/company'),$params['company']);
        $this->type($this->getUiElement('inputs/street1'),$params['street1']);
        $this->type($this->getUiElement('inputs/street2'),$params['street2']);
        $this->type($this->getUiElement('inputs/city'),$params['city']);
        $this->type($this->getUiElement('inputs/postcode'),$params['postcode']);
        $this->type($this->getUiElement('inputs/telephone'),$params['telephone']);
        //Country and Region
        $this->selectCountry($this->getUiElement('selectors/country'),$params['country']);
        $this->selectRegion($this->getUiElement('selectors/region'),$params['region']);
        // Save address
        $this->clickAndWait($this->getUiElement('buttons/saveAddress'));
        // check for error message
        if ($this->waitForElement($this->getUiElement("/admin/messages/error"),1)) {
            $etext = $this->getText($this->getUiElement("/admin/messages/error"));
            $this->setVerificationErrors("Adding new address: " . $etext);
            return false;
        } else {
        // Check for success message
          if (!$this->waitForElement($this->getUiElement("/admin/messages/success"),1)) {
            $this->setVerificationErrors("Adding new address: no success message");
          }
          return false;
        }
        // Change ShippingAddress for last item to Second Address
        $this->setUiNamespace('/frontend/pages/multiShippingCheckout/tabs/');
        $secondAddressIndex = $this->findAddressByMask($this->getUiElement('selectAddresses/elements/lastShippingAddress'), '/Second Address/');
        $secondAddressOptionXpath = $this->getUiElement('selectAddresses/elements/lastShippingAddress') . '/option' . '[' . $secondAddressIndex . ']';
        $secondAddressText = $this->getText($secondAddressOptionXpath);
        $this->select($this->getUiElement('selectAddresses/elements/lastShippingAddress'), 'label=' . $secondAddressText );

        //Press 'Continue to shipping information'
        $this->clickAndWait($this->getUiElement('selectAddresses/buttons/continue'));

        //Select Free shipping for all items
        $paneXpath = $this->getUiElement('shippingInformation/elements/addressPane');
        $count = $this -> getXpathCount($paneXpath);
        Core::debug('address count:'.$count);
        for ($i=1; $i<=$count; $i++) {
            $this->click($paneXpath . '[' . $i . "]//label[contains(text(),'Free')]");
        };

        //Continue
        $this->clickAndWait($this->getUiElement('shippingInformation/buttons/continue'));

        // Fill billibg Information fields
        $this->click($this->getUiElement('billingInformation/inputs/check'));
        $this->clickAndWait($this->getUiElement('billingInformation/buttons/continue'));

        //Place order
        $this->clickAndWait($this->getUiElement('placeOrder/buttons/placeOrder'));

        // Check for success message
        if (!$this->waitForElement($this->getUiElement('orderSuccess/messages/orderSuccess'),10)) {
            $this->setVerificationErrors("Check 1: no 'Order Placed'  message");
            return false;
        }
        return true;
    }

   /**
     * Perform Checkout with login from FrontEnd
     * @param - array wirh expecteded values:
     *       email
     *       password
     *       productUrl
     *       qty
     */
    public function multiShippingLoginCheckout($params)
    {
        //Open ProductPage, place one to ShoppingCart, Press "Proceed to Checkout"
        $this->startCheckout($params, true);

        //Login
        $this->setUiNamespace('/frontend/pages/multiShippingCheckout/checkoutMethod/');
        $this->type($this->getUiElement('inputs/email'),$params['email']);
        $this->type($this->getUiElement('inputs/password'),$params['password']);
        $this->clickAndWait($this->getUiElement('buttons/login'));
        // check for error message
        if ($this->waitForElement($this->getUiElement('/admin/messages/error'),1)) {
            $etext = $this->getText($this->getUiElement('/admin/messages/error'));
            $this->setVerificationErrors("Login Error: " . $etext);
            return false;
        }


        // Change ShippingAddress for last item to Second Address
        $this->setUiNamespace('/frontend/pages/multiShippingCheckout/tabs/');
        $secondAddressIndex = $this->findAddressByMask($this->getUiElement('selectAddresses/elements/lastShippingAddress'), '/Second Address/');
        $secondAddressOptionXpath = $this->getUiElement('selectAddresses/elements/lastShippingAddress') . '/option' . '[' . $secondAddressIndex . ']';
        $secondAddressText = $this->getText($secondAddressOptionXpath);
        $this->select($this->getUiElement('selectAddresses/elements/lastShippingAddress'), 'label=' . $secondAddressText );

        //Press 'Continue to shipping information'
        $this->clickAndWait($this->getUiElement('selectAddresses/buttons/continue'));

        //Select Free shipping for all items
        $paneXpath = $this->getUiElement('shippingInformation/elements/addressPane');
        $count = $this -> getXpathCount($paneXpath);
        Core::debug('address count:'.$count);
        for ($i=1; $i<=$count; $i++) {
            $this->click($paneXpath . '[' . $i . "]//label[contains(text(),'Free')]");
        };

        //Continue
        $this->clickAndWait($this->getUiElement('shippingInformation/buttons/continue'));

        // Fill billibg Information fields
        $this->click($this->getUiElement('billingInformation/inputs/check'));
        $this->clickAndWait($this->getUiElement('billingInformation/buttons/continue'));

        //Place order
        $this->clickAndWait($this->getUiElement('placeOrder/buttons/placeOrder'));

        // Check for success message
        if (!$this->waitForElement($this->getUiElement('orderSuccess/messages/orderSuccess'),10)) {
            $this->setVerificationErrors('Check 1: no "Order Placed"  message');
            return false;
        }
        return true;
    }

    /* Test-specific utilitary functions
     *
     */

    /*
     * Open product page, place one to ShoppingCart, Proceed to Checkout
     * @params - array with expected values of:
     * productUrl
     * qty
     * isMultiple - if set, start multiShippingCheckout instead of ordinal one
     */
    function startCheckout($params, $isMultiple = false)
    {
        $this->printDebug('startCheckout started...');
        //Open product page
        $this->open($params['productUrl']);

        $this->setUiNamespace('/frontend/pages/product/');

        // Place product to the cart
        $this->type($this->getUiElement('inputs/qty'),$params['qty']);
        $this->clickAndWait($this->getUiElement('buttons/addToCart'));
        $this->printInfo('Placing ' . $params['productUrl'] . ' to cart');

        //Proceed to checkout
        if ($isMultiple) {
            $this->printInfo('Starting multipleShipping checkout');
            $this->clickAndWait($this->getUiElement('/frontend/pages/shoppingCart/links/multipleShippingCheckout'));
        } else {
            $this->printInfo('Starting ordinal checkout');
            $this->clickAndWait($this->getUiElement('/frontend/pages/shoppingCart/buttons/proceedToCheckout'));
        }
        $this->printDebug('startCheckout finished');
    }

    /*
     * Sequentally fill all fields in the BillingInformation Checkout Step
     * @params - array with expected values of:
     * firstName
     * lastName
     * company
     * email
     * street1
     * street2
     * city
     * postcode
     * telephone
     * fax
     * country
     * region
     */
    function fillBillingTab($params)
    {
        $this->printDebug('fillBillingTab() started...');

        $this->setUiNamespace('frontend/pages/onePageCheckout/tabs/billingAddress/');

        $this->type($this->getUiElement('inputs/firstName'),$params['firstName']);
        $this->type($this->getUiElement('inputs/lastName'),$params['lastName']);
        $this->type($this->getUiElement('inputs/company'),$params['company']);
        $this->type($this->getUiElement('inputs/email'),$params['email']);
        $this->type($this->getUiElement('inputs/street1'),$params['street1']);
        $this->type($this->getUiElement('inputs/street2'),$params['street2']);
        $this->type($this->getUiElement('inputs/city'),$params['city']);
        $this->type($this->getUiElement('inputs/postcode'),$params['postcode']);
        $this->type($this->getUiElement('inputs/telephone'),$params['telephone']);
        $this->type($this->getUiElement('inputs/fax'),$params['fax']);
        //Country and Region
        $this->selectCountry($this->getUiElement('selectors/country'),$params['country']);
        $this->selectRegion($this->getUiElement('selectors/region'),$params['region']);
        //Use billing address for shipping
        $this->click($this->getUiElement('inputs/use_for_shipping'));
        $this->printDebug('fillBillingTab finished...');
    }

    /*
     *  Sequentally fill all fields in the ShippingMethod, PaymentInfo, OrderReview Checkout Steps
     *  used free shipping and check/money order options
     */
    function shippingMethodPaymentPlaceOrderSteps($params)
    {
         $this->printDebug('shippingMethodPaymentPlaceOrderSteps started...');

         $this->setUiNamespace('/frontend/pages/onePageCheckout/tabs/');

         //Fill Shipping Method Tab
         if (!$this->waitForElement($this->getUiElement('shippingMethod/inputs/freeShipping'),10)) {
            $this->setVerificationErrors('Check 3: no Free shipping method available.');
            return false;
         }
         $this->printInfo('Using Free shipping');
         $this->click($this->getUiElement('shippingMethod/inputs/freeShipping'));
         $this->click($this->getUiElement('shippingMethod/buttons/continue'));
         $this->pleaseWaitStep($this->getUiElement('shippingMethod/elements/pleaseWait'));

         //Fill Payment Information Tab
         if (!$this->waitForElement($this->getUiElement('paymentInfo/inputs/check'),10)) {
            $this->setVerificationErrors("Check 4: 'Check / MoneyOrder' payment method is not available.");
            return false;
         }

         $this->printInfo('Using Check/Money Order method');
         $this->click($this->getUiElement('paymentInfo/inputs/check'));
         $this->click($this->getUiElement('paymentInfo/buttons/continue'));
         $this->pleaseWaitStep($this->getUiElement('paymentInfo/elements/pleaseWait'));
         //Place Order
         $this->clickAndWait($this->getUiElement('orderReview/buttons/placeOrder'));

         // Check for success message
         if (!$this->waitForElement($this->getUiElement('/frontend/pages/onePageCheckout/messages/orderPlaced'),10)) {
            $this->setVerificationErrors("Check 5: no 'Order Placed'  message");
            return false;
         }

         if (!$this->waitForElement($this->getUiElement('orderPlaced/links/orderID'),10)) {
            $this->setVerificationErrors("Check 6: no 'OrderID'  element on the page");
            return false;
         }
         $orderID = $this->getText($this->getUiElement('orderPlaced/links/orderID'));

         $this->printInfo($orderID);
         $this->printDebug('shippingMethodPaymentPlaceOrderSteps finished');
         return true;
    }

    /*
     * wait for appearance and disappearence of 'Loading Next step...' block during frontend checkout
     * Since all steps has unique block id, its should be passed as parameter
     * @param - ID of '$element-please-wait' block
     */
    public function pleaseWaitStep($element)
    {
        $this->printDebug('pleaseWaitStep started :' . $element);
        //*[@id='billing-please-wait' and contains(@style,'display: none')]
        // await for appear and disappear 'Please wait' animated gif...
            for ($second = 0; ; $second++) {
                if ($second >= 2)  {
                    break;
                }
                try {
                    if (!$this->isElementPresent("//*[@id='" . $element . "' and contains(@style,'display: none')]")) {
                        break;
                    }
                } catch (Exception $e) {

                }
                sleep(1);
            }

            for ($second = 0; ; $second++) {
                if ($second >= 20)break;
                try {
                    if ($this->isElementPresent("//*[@id='" . $element . "' and contains(@style,'display: none')]")) {
                        break;
                    }
                } catch (Exception $e) {

                }
                sleep(1);
            }
            sleep(1);
        $this->printDebug('pleaseWaitStep finished :');
    }
}
