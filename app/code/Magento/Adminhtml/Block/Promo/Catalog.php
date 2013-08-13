<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog price rules
 *
 * @category    Magento
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Promo_Catalog extends Mage_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_addButton('apply_rules', array(
            'label'     => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Apply Rules'),
            'onclick'   => "location.href='".$this->getUrl('*/*/applyRules')."'",
            'class'     => 'apply',
        ));

        $this->_controller = 'promo_catalog';
        $this->_headerText = Mage::helper('Magento_CatalogRule_Helper_Data')->__('Catalog Price Rules');
        $this->_addButtonLabel = Mage::helper('Magento_CatalogRule_Helper_Data')->__('Add New Rule');
        parent::_construct();

    }
}
