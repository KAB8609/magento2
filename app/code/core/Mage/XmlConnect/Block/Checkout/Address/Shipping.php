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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout shipping addresses xml renderer
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Address_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    /**
     * Render billing shipping xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $shippingXmlObj = new Varien_Simplexml_Element('<shipping></shipping>');

        $addressId = $this->getAddress()->getId();
        if (empty($addressId)) {
            $address = $this->getCustomer()->getPrimaryShippingAddress();
            if ($address) {
                $addressId = $address->getId();
            }
        }

        foreach ($this->getCustomer()->getAddresses() as $address) {
            $item = $shippingXmlObj->addChild('item');
            if ($addressId == $address->getId()) {
                $item->addAttribute('selected', 1);
            }
            $item->addChild('address_id', $address->getId());
            $item->addChild('address_line', $shippingXmlObj->xmlentities(strip_tags($address->format('oneline_noescape'))));
        }

        return $shippingXmlObj->asNiceXml();
    }

}