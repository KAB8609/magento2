<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Controller_Catalog_Product_Group extends Magento_Adminhtml_Controller_Action
{
    public function saveAction()
    {
        $model = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Group');

        $model->setAttributeGroupName($this->getRequest()->getParam('attribute_group_name'))
              ->setAttributeSetId($this->getRequest()->getParam('attribute_set_id'));

        if( $model->itemExists() ) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Catalog_Helper_Data')->__('A group with the same name already exists.'));
        } else {
            try {
                $model->save();
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Catalog_Helper_Data')->__('Something went wrong while saving this group.'));
            }
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Catalog::products');
    }
}
