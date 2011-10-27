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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Render shopping cart xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $cartMessages   = $this->getMessages();
        $quote          = $this->getQuote();
        /** @var $xmlObject Mage_XmlConnect_Model_Simplexml_Element */
        $xmlObject      = Mage::getModel('xmlconnect/simplexml_element', '<cart></cart>');
        $xmlObject->addAttribute('is_virtual', (int)$this->helper('Mage_Checkout_Helper_Cart')->getIsVirtualQuote());
        $xmlObject->addAttribute('summary_qty', (int)$this->helper('Mage_Checkout_Helper_Cart')->getSummaryCount());
        if (strlen($quote->getCouponCode())) {
            $xmlObject->addAttribute('has_coupon_code', 1);
        }
        $products = $xmlObject->addChild('products');
         /* @var $item Mage_Sales_Model_Quote_Item */
        foreach ($this->getItems() as $item) {
            $type = $item->getProductType();
            $renderer = $this->getItemRenderer($type)->setItem($item);
            /**
             * General information
             */
            $itemXml = $products->addChild('item');
            $itemXml->addChild('entity_id', $item->getProduct()->getId());
            $itemXml->addChild('entity_type', $type);
            $itemXml->addChild('item_id', $item->getId());
            $itemXml->addChild('name', $xmlObject->xmlentities($renderer->getProductName()));
            $itemXml->addChild('code', 'cart[' . $item->getId() . '][qty]');
            $itemXml->addChild('qty', $renderer->getQty());
            $icon = $renderer->getProductThumbnail()->resize(
                Mage::helper('Mage_XmlConnect_Helper_Image')->getImageSizeForContent('product_small')
            );
            $iconXml = $itemXml->addChild('icon', $icon);
            $file = Mage::helper('Mage_XmlConnect_Helper_Data')->urlToPath($icon);
            $iconXml->addAttribute('modification_time', filemtime($file));
            /**
             * Price
             */
            $exclPrice = $inclPrice = 0;
            if ($this->helper('Mage_Tax_Helper_Data')->displayCartPriceExclTax() || $this->helper('Mage_Tax_Helper_Data')->displayCartBothPrices()) {
                if (Mage::helper('Mage_Weee_Helper_Data')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                    && $item->getWeeeTaxAppliedAmount()
                ) {
                    $exclPrice = $item->getCalculationPrice() + $item->getWeeeTaxAppliedAmount()
                        + $item->getWeeeTaxDisposition();
                } else {
                    $exclPrice = $item->getCalculationPrice();
                }
            }
            if ($this->helper('Mage_Tax_Helper_Data')->displayCartPriceInclTax() || $this->helper('Mage_Tax_Helper_Data')->displayCartBothPrices()) {
                $_incl = $this->helper('Mage_Checkout_Helper_Data')->getPriceInclTax($item);
                if (Mage::helper('Mage_Weee_Helper_Data')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                    && $item->getWeeeTaxAppliedAmount()
                ) {
                    $inclPrice = $_incl + $item->getWeeeTaxAppliedAmount();
                } else {
                    $inclPrice = $_incl - $item->getWeeeTaxDisposition();
                }
            }
            $exclPrice = Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml($exclPrice);
            $paypalPrice = Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml($item->getCalculationPrice());
            $formattedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);
            $inclPrice = Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml($inclPrice);
            $formattedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);

            $priceXmlObj = $itemXml->addChild('price');
            $formattedPriceXmlObj = $itemXml->addChild('formated_price');

            if ($this->helper('Mage_Tax_Helper_Data')->displayCartBothPrices()) {
                $priceXmlObj->addAttribute('excluding_tax', $exclPrice);
                $priceXmlObj->addAttribute('including_tax', $inclPrice);

                $formattedPriceXmlObj->addAttribute('excluding_tax', $formattedExclPrice);
                $formattedPriceXmlObj->addAttribute('including_tax', $formattedInclPrice);
            } else {
                if ($this->helper('Mage_Tax_Helper_Data')->displayCartPriceExclTax()) {
                    $priceXmlObj->addAttribute('regular', $exclPrice);
                    $formattedPriceXmlObj->addAttribute('regular', $formattedExclPrice);
                }
                if ($this->helper('Mage_Tax_Helper_Data')->displayCartPriceInclTax()) {
                    $priceXmlObj->addAttribute('regular', $inclPrice);
                    $formattedPriceXmlObj->addAttribute('regular', $formattedInclPrice);
                }
            }

            /**
             * Info for paypal MEP if it's enabled
             */
            $appConfig = Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication()->loadConfiguration()->getRenderConf();

            $isMepActive = $appConfig['paypal']['isActive'];

            $paypalMepIsAvailable = Mage::getModel('xmlconnect/payment_method_paypal_mep')->isAvailable(null);
            if ($paypalMepIsAvailable && $isMepActive) {
                $paypalPriceXmlObj = $itemXml->addChild('paypal_price');
                $paypalPriceXmlObj->addAttribute('regular', $paypalPrice);
                $paypalPriceXmlObj->addAttribute(
                    'subtotal', Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml($item->getRowTotal())
                );
            }

            /**
             * Subtotal
             */
            $exclPrice = $inclPrice = 0;
            if ($this->helper('Mage_Tax_Helper_Data')->displayCartPriceExclTax() || $this->helper('Mage_Tax_Helper_Data')->displayCartBothPrices()) {
                if (Mage::helper('Mage_Weee_Helper_Data')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                    && $item->getWeeeTaxAppliedAmount()
                ) {
                    $exclPrice = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                        + $item->getWeeeTaxRowDisposition();
                } else {
                     $exclPrice = $item->getRowTotal();
                }
            }
            if ($this->helper('Mage_Tax_Helper_Data')->displayCartPriceInclTax() || $this->helper('Mage_Tax_Helper_Data')->displayCartBothPrices()) {
                $_incl = $this->helper('Mage_Checkout_Helper_Data')->getSubtotalInclTax($item);
                if (Mage::helper('Mage_Weee_Helper_Data')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                    && $item->getWeeeTaxAppliedAmount()
                ) {
                    $inclPrice = $_incl + $item->getWeeeTaxAppliedRowAmount();
                } else {
                    $inclPrice = $_incl - $item->getWeeeTaxRowDisposition();
                }
            }

            $exclPrice = Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml($exclPrice);
            $formattedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);

            $inclPrice = Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml($inclPrice);
            $formattedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);

            $subtotalPriceXmlObj = $itemXml->addChild('subtotal');
            $subtotalFormattedPriceXmlObj = $itemXml->addChild('formated_subtotal');

            if ($this->helper('Mage_Tax_Helper_Data')->displayCartBothPrices()) {
                $subtotalPriceXmlObj->addAttribute('excluding_tax', $exclPrice);
                $subtotalPriceXmlObj->addAttribute('including_tax', $inclPrice);

                $subtotalFormattedPriceXmlObj->addAttribute('excluding_tax', $formattedExclPrice);
                $subtotalFormattedPriceXmlObj->addAttribute('including_tax', $formattedInclPrice);
            } else {
                if ($this->helper('Mage_Tax_Helper_Data')->displayCartPriceExclTax()) {
                    $subtotalPriceXmlObj->addAttribute('regular', $exclPrice);
                    $subtotalFormattedPriceXmlObj->addAttribute('regular', $formattedExclPrice);
                }
                if ($this->helper('Mage_Tax_Helper_Data')->displayCartPriceInclTax()) {
                    $subtotalPriceXmlObj->addAttribute('regular', $inclPrice);
                    $subtotalFormattedPriceXmlObj->addAttribute('regular', $formattedInclPrice);
                }
            }

            /**
             * Options list
             */
            if ($_options = $renderer->getOptionList()) {
                $itemOptionsXml = $itemXml->addChild('options');
                foreach ($_options as $_option) {
                    $_formattedOptionValue = $renderer->getFormatedOptionValue($_option);
                    $optionXml = $itemOptionsXml->addChild('option');
                    $optionXml->addAttribute('label', $xmlObject->xmlAttribute($_option['label']));
                    $optionXml->addAttribute(
                        'text', $xmlObject->xmlAttribute(strip_tags($_formattedOptionValue['value']))
                    );
                }
            }

            /**
             * Item messages
             */
            if ($messages = $renderer->getMessages()) {
                $itemMessagesXml = $itemXml->addChild('messages');
                foreach ($messages as $message) {
                    $messageXml = $itemMessagesXml->addChild('option');
                    $messageXml->addChild('type', $message['type']);
                    $messageXml->addChild('text', $xmlObject->xmlentities($message['text']));
                }
            }
        }

        /**
         * Cart messages
         */
        if ($cartMessages) {
            $messagesXml = $xmlObject->addChild('messages');
            foreach ($cartMessages as $status => $messages) {
                foreach ($messages as $message) {
                    $messageXml = $messagesXml->addChild('message');
                    $messageXml->addChild('status', $status);
                    $messageXml->addChild('text', strip_tags($message));
                }
            }
        }

        /**
         * Cross Sell Products
         */
        if (count($this->getItems())) {
            $crossellXml = $this->getChildHtml('crosssell');
        } else {
            $crossellXml = '<crosssell></crosssell>';
        }

        $crossSellXmlObj = Mage::getModel('xmlconnect/simplexml_element', $crossellXml);
        $xmlObject->appendChild($crossSellXmlObj);

        return $xmlObject->asNiceXml();
    }
}
