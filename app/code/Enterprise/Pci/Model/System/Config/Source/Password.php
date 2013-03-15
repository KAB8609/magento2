<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for admin password change mode
 *
 */
class Enterprise_Pci_Model_System_Config_Source_Password extends Varien_Object
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => Mage::helper('Enterprise_Pci_Helper_Data')->__('Recommended'),
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('Enterprise_Pci_Helper_Data')->__('Forced'),
            ),
        );
    }
}
