<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Versioning configuration source model
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Source_Versioning
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => Mage::helper('Enterprise_Cms_Helper_Data')->__('Enabled by Default'),
            '1' => Mage::helper('Enterprise_Cms_Helper_Data')->__('Disabled by Default')
        );
    }
}
