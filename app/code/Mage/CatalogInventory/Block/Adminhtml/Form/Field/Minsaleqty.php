<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog inventory "Minimum Qty Allowed in Shopping Cart" field
 *
 * @category   Mage
 * @package    Magento_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Block_Adminhtml_Form_Field_Minsaleqty
    extends Mage_Backend_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var Magento_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_groupRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return Magento_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                'Magento_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup', '',
                array('data' => array('is_render_to_js_template' => true))
            );
            $this->_groupRenderer->setClass('customer_group_select');
            $this->_groupRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_groupRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('customer_group_id', array(
            'label' => Mage::helper('Mage_Customer_Helper_Data')->__('Customer Group'),
            'renderer' => $this->_getGroupRenderer(),
        ));
        $this->addColumn('min_sale_qty', array(
            'label' => Mage::helper('Magento_CatalogInventory_Helper_Data')->__('Minimum Qty'),
            'style' => 'width:100px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('Magento_CatalogInventory_Helper_Data')->__('Add Minimum Qty');
    }

    /**
     * Prepare existing row data object
     *
     * @param Magento_Object
     */
    protected function _prepareArrayRow(Magento_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('customer_group_id')),
            'selected="selected"'
        );
    }
}
