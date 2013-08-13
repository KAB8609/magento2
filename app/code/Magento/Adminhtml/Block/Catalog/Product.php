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
 * Catalog manage products block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product extends Magento_Adminhtml_Block_Widget_Container
{
    protected $_template = 'catalog/product.phtml';

    /**
     * Prepare button and grid
     *
     * @return Magento_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
        $addButtonProps = array(
            'id' => 'add_new_product',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Add Product'),
            'class' => 'btn-add',
            'button_class' => 'btn-round',
            'class_name' => 'Mage_Backend_Block_Widget_Button_Split',
            'options' => $this->_getAddProductButtonOptions(),
        );
        $this->_addButton('add_new', $addButtonProps);

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Grid', 'product.grid')
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
