<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Item Attributes Grid Block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Item_Attribute_Grid
    extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Initialize grid, set grid Id
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rmaItemAttributeGrid');
        $this->setDefaultSort('sort_order');
    }

    /**
     * Prepare customer attributes grid collection object
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Enterprise_Rma_Model_Resource_Item_Attribute_Collection')
            ->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare customer attributes grid columns
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('is_visible', array(
            'header'    => Mage::helper('Enterprise_Rma_Helper_Data')->__('Visible on Frontend'),
            'sortable'  => true,
            'index'     => 'is_visible',
            'type'      => 'options',
            'options'   => array(
                '0' => Mage::helper('Enterprise_Rma_Helper_Data')->__('No'),
                '1' => Mage::helper('Enterprise_Rma_Helper_Data')->__('Yes'),
            ),
            'header_css_class'  => 'col-visible-on-front',
            'column_css_class'  => 'col-visible-on-front'
        ));

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('Enterprise_Rma_Helper_Data')->__('Sort Order'),
            'sortable'  => true,
            'align'     => 'center',
            'index'     => 'sort_order',
            'header_css_class'  => 'col-order',
            'column_css_class'  => 'col-order'
        ));

        return $this;
    }
}
