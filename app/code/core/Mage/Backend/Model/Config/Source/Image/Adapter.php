<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Image_Adapter
{
    /**
     * Return hash of image adapter codes and labels
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Varien_Image_Adapter::ADAPTER_IM  => Mage::helper('Mage_Adminhtml_Helper_Data')->__('ImageMagick'),
            Varien_Image_Adapter::ADAPTER_GD2 => Mage::helper('Mage_Adminhtml_Helper_Data')->__('PHP GD2'),
        );
    }
}
