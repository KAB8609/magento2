<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cart Item Configure block
 * Updates templates and blocks to show 'Update Cart' button and set right form submit url
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @module     Checkout
 */
class Mage_Checkout_Block_Cart_Item_Configure extends Mage_Core_Block_Template
{

    /**
     * Configure product view blocks
     *
     * @return Mage_Checkout_Block_Cart_Item_Configure
     */
    protected function _prepareLayout()
    {
        // Set custom submit url route for form - to submit updated options to cart
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
             $block->setSubmitRouteData(array(
                'route' => 'checkout/cart/updateItemOptions',
                'params' => array('id' => $this->getRequest()->getParam('id'))
             ));
        }

        // Set custom template with 'Update Cart' button
        $block = $this->getLayout()->getBlock('product.info.addtocart');
        if ($block) {
            $block->setTemplate('cart/item/configure/updatecart.phtml');
        }

        return parent::_prepareLayout();
    }
}
