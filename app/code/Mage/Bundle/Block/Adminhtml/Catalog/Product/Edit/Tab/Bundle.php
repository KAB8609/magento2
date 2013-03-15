<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog product bundle items tab block
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_product = null;

    protected $_template = 'product/edit/bundle.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setSkipGenerateContent(true);

    }

    public function getTabUrl()
    {
        return $this->getUrl('*/bundle_product_edit/form', array('_current' => true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Prepare layout
     *
     * @return Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label' => Mage::helper('Mage_Bundle_Helper_Data')->__('Create New Option'),
            'class' => 'add',
            'id'    => 'add_new_option',
            'on_click' => 'bOption.add()'
        ));

        $this->setChild('options_box',
            $this->getLayout()->createBlock('Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option',
                'adminhtml.catalog.product.edit.tab.bundle.option')
        );

        return parent::_prepareLayout();
    }

    /**
     * Check block readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getCompositeReadonly();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }

    public function getFieldSuffix()
    {
        return 'product';
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    public function getTabLabel()
    {
        return Mage::helper('Mage_Bundle_Helper_Data')->__('Bundle Items');
    }
    public function getTabTitle()
    {
        return Mage::helper('Mage_Bundle_Helper_Data')->__('Bundle Items');
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
}
