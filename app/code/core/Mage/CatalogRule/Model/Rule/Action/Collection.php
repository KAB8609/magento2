<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_CatalogRule_Model_Rule_Action_Collection extends Mage_Rule_Model_Action_Collection
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('Mage_CatalogRule_Model_Rule_Action_Collection');
    }

    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        $actions = array_merge_recursive($actions, array(
            array('value'=>'Mage_CatalogRule_Model_Rule_Action_Product', 'label'=>Mage::helper('Mage_CatalogInventory_Helper_Data')->__('Update the Product'))
        ));
        return $actions;
    }
}
