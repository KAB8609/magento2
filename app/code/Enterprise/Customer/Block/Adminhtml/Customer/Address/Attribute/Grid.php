<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Address Attributes Grid Block
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Grid
    extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Initialize grid, set grid Id
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('sort_order');
        $this->setId('customerAddressAttributeGrid');
    }

    /**
     * Prepare customer address attributes grid collection object
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Customer_Model_Resource_Address_Attribute_Collection */
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Address_Attribute_Collection')
            ->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare customer address attributes grid columns
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('is_visible', array(
            'header'    => __('Visible to Customer'),
            'sortable'  => true,
            'index'     => 'is_visible',
            'type'      => 'options',
            'options'   => array(
                '0' => __('No'),
                '1' => __('Yes'),
            ),
            'align'     => 'center',
        ));

        $this->addColumn('sort_order', array(
            'header'    => __('Sort Order'),
            'sortable'  => true,
            'align'     => 'center',
            'index'     => 'sort_order'
        ));

        return $this;
    }
}
