<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Users
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User statuses option array
 *
 * @category   Enterprise
 * @package    Enterprise_GiftWrapping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Model_MassOptions implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('label' => '', 'value' => ''),
            array('label' => __('Enabled'), 'value' => '1'),
            array('label' => __('Disabled'), 'value' => '0')
        );
    }
}
