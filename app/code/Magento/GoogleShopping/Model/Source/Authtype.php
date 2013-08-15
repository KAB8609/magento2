<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Data Api authorization types Source
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Source_Authtype
{
    /**
     * Retrieve option array with authentification types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'authsub', 'label' => Mage::helper('Magento_GoogleShopping_Helper_Data')->__('AuthSub')),
            array('value' => 'clientlogin', 'label' => Mage::helper('Magento_GoogleShopping_Helper_Data')->__('ClientLogin'))
        );
    }
}
