<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Hierarchy Navigation Menu source model for list type
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Model_Source_Hierarchy_Menu_Listtype
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '0'  => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Unordered'),
            '1' => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Ordered'),
        );
    }
}
