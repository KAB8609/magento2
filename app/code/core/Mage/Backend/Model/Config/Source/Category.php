<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config category source
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Category
{
    public function toOptionArray($addEmpty = true)
    {
        $tree = Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Tree');

        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Collection');

        $collection->addAttributeToSelect('name')
            ->addRootLevelFilter()
            ->load();

        $options = array();

        if ($addEmpty) {
            $options[] = array(
                'label' => Mage::helper('Mage_Backend_Helper_Data')->__('-- Please Select a Category --'),
                'value' => ''
            );
        }
        foreach ($collection as $category) {
            $options[] = array(
               'label' => $category->getName(),
               'value' => $category->getId()
            );
        }

        return $options;
    }
}
