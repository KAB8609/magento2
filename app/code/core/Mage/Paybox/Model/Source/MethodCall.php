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
 * @category   Mage
 * @package    Mage_Paybox
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * Paybox Method used of call of "url http" Dropdown source
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paybox_Model_Source_MethodCall
{
    public function toOptionArray()
    {
        return array(
//            array('value' => Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_AUTH, 'label' => Mage::helper('paypal')->__('Authorization')),
            array('value' => 'GET', 'label' => Mage::helper('paybox')->__('GET')),
            array('value' => 'POST', 'label' => Mage::helper('paybox')->__('POST')),
        );
    }
}
