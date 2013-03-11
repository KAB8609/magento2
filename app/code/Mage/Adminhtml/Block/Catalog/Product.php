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
 * Catalog manage products block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_template = 'catalog/product.phtml';

    /**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
        /** @var $limitation Mage_Catalog_Model_Product_Limitation */
        $limitation = Mage::getObjectManager()->get('Mage_Catalog_Model_Product_Limitation');
        if (!$limitation->isCreateRestricted()) {
            $this->_addButton('add_new', array(
                'id' => 'add_new_product',
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Add Product'),
                'class' => 'btn-add',
                'class_name' => 'Mage_Backend_Block_Widget_Button_Split',
                'options' => $this->_getAddProductButtonOptions()
            ));
        }

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Grid', 'product.grid')
        );
        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function _getAddProductButtonOptions()
    {
        $splitButtonOptions = array();

        foreach (Mage::getModel('Mage_Catalog_Model_Product_Type')->getOptionArray() as $key => $label) {
            $splitButtonOptions[$key] = array(
                'label'     => $label,
                'onclick'   => "setLocation('" . $this->_getProductCreateUrl($key) . "')",
                'default'   => Mage_Catalog_Model_Product_Type::DEFAULT_TYPE == $key
            );
        }

        return $splitButtonOptions;
    }

    /**
     * Retrieve product create url by specified product type
     *
     * @param string $type
     * @return string
     */
    protected function _getProductCreateUrl($type)
    {
        return $this->getUrl('*/*/new', array(
            'set'   => Mage::getModel('Mage_Catalog_Model_Product')->getDefaultAttributeSetId(),
            'type'  => $type
        ));
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return Mage::app()->isSingleStoreMode();
    }
}
