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
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * Payflow Payment Action Dropdown source
 *
 * @author     Yuriy Scherbina <yuriy.scherbina@varien.com>
 */
class Mage_Paygate_Model_Payflow_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Paygate_Model_Payflow_Pro::TRXTYPE_AUTH_ONLY, 'label' => Mage::helper('paypal')->__('Authorize Only')),
            array('value' => Mage_Paygate_Model_Payflow_Pro::TRXTYPE_SALE, 'label' => Mage::helper('paypal')->__('Authorize and Capture')),
        );
    }
}