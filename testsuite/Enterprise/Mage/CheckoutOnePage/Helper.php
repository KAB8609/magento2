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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class Core_Mage_for OnePageCheckout
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CheckoutOnePage_Helper extends Core_Mage_CheckoutOnePage_Helper
{
    /**
     * Adding gift message(gift wrapping) for entire order of each item
     *
     * @param array $giftOptions
     *
     */
    public function frontAddGiftMessage(array $giftOptions)
    {
        if (isset($giftOptions['entire_order'])) {
            $this->fillCheckbox('add_gift_options', 'Yes');
            $this->fillCheckbox('gift_option_for_order', 'Yes');
            $this->fillForm($giftOptions['entire_order']);
            if (isset($giftOptions['entire_order']['gift_message'])) {
                $this->clickControl('link', 'order_gift_message', false);
                $this->fillForm($giftOptions['entire_order']['gift_message']);
            }
        }
        if (isset($giftOptions['individual_items'])) {
            $this->fillCheckbox('add_gift_options', 'Yes');
            $this->fillCheckbox('gift_option_for_item', 'Yes');
            foreach ($giftOptions['individual_items'] as $key => $data) {
                $this->addParameter('productName', $key);
                $this->fillForm($data);
                if (isset($data['gift_message'])) {
                    $this->clickControl('link', 'item_gift_message', false);
                    $this->fillForm($data['gift_message']);
                }
            }
        }
    }
    
    /**
     * @return string
     */
    public function submitOnePageCheckoutOrder()
    {
        $this->clickButton('place_order', false);
        $this->waitForElementOrAlert(array($this->_getMessageXpath('success_checkout'),
                                           $this->_getMessageXpath('general_error'),
                                           $this->_getMessageXpath('general_validation')));
        $this->assertTrue($this->verifyNotPresetAlert(), $this->getMessagesOnPage());
        $this->assertMessageNotPresent('error');
        $this->validatePage('onepage_checkout_success');
        $xpath = $this->_getControlXpath('link', 'order_number');
        if ($this->isElementPresent($xpath)) {
            return $this->getText($xpath);
        }

        return preg_replace('/[^0-9]/', '', $this->getText("//*[contains(text(),'Your order')]"));
    }
}