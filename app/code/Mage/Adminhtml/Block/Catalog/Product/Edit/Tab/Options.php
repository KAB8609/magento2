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
 * customers defined options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget
{
    protected $_template = 'catalog/product/edit/options.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('add_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Add New Option'),
            'class' => 'add',
            'id'    => 'add_new_defined_option'
        ));

        $this->addChild('options_box', 'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option');

        $this->addChild('import_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Import Options'),
            'class' => 'add',
            'id'    => 'import_new_defined_option'
        ));

        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }
}
