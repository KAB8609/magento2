<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product option types mode source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Source_Product_Options_Type
{
    const PRODUCT_OPTIONS_GROUPS_PATH = 'global/catalog/product/options/custom/groups';

    public function toOptionArray()
    {
        $groups = array(
            array('value' => '', 'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('-- Please select --'))
        );

        foreach (Mage::getConfig()->getNode(self::PRODUCT_OPTIONS_GROUPS_PATH)->children() as $group) {
            $types = array();
            $typesPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types';
            foreach (Mage::getConfig()->getNode($typesPath)->children() as $type) {
                $labelPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types/' . $type->getName() . '/label';
                $types[] = array(
                    'label' => (string) Mage::getConfig()->getNode($labelPath),
                    'value' => $type->getName()
                );
            }

            $labelPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/label';

            $groups[] = array(
                'label' => (string) Mage::getConfig()->getNode($labelPath),
                'value' => $types
            );
        }

        return $groups;
    }
}
