<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User statuses option array
 *
 * @category   Mage
 * @package    Mage_User
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Model_Statuses implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => __('Active'),
            '0' => __('Inactive'),
        );
    }
}
