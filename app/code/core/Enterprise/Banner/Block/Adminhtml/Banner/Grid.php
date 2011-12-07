<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set defaults
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('bannerGrid');
        $this->setDefaultSort('banner_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('banner_filter');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return Enterprise_Banner_Block_Adminhtml_Banner_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Enterprise_Banner_Model_Resource_Banner_Collection')
            ->addStoresVisibility();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     */
    protected function _prepareColumns()
    {
        $this->addColumn('banner_id',
            array(
                'header'=> Mage::helper('Enterprise_Banner_Helper_Data')->__('ID'),
                'width' => 1,
                'type'  => 'number',
                'index' => 'banner_id',
        ));

        $this->addColumn('banner_name', array(
            'header' => Mage::helper('Enterprise_Banner_Helper_Data')->__('Banner Name'),
            'type'   => 'text',
            'index'  => 'name',
            'escape' => true
        ));

        $this->addColumn('banner_types', array(
            'header'  => Mage::helper('Enterprise_Banner_Helper_Data')->__('Banner Types'),
            'type'    => 'options',
            'options' => Mage::getSingleton('Enterprise_Banner_Model_Config')->toOptionArray(true, false),
            'index'   => 'types',
            'width'   => 250,
            'filter'  => false, // TODO implement
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'                => Mage::helper('Enterprise_Banner_Helper_Data')->__('Visible In'),
                'type'                  => 'store',
                'index'                 => 'stores',
                'sortable'              => false,
                'store_view'            => true,
                'width'                 => 200
            ));
        }

        $this->addColumn('banner_is_enabled',
            array(
                'header'    => Mage::helper('Enterprise_Banner_Helper_Data')->__('Active'),
                'align'     => 'center',
                'width'     => 1,
                'index'     => 'is_enabled',
                'type'      => 'options',
                'options'   => array(
                    Enterprise_Banner_Model_Banner::STATUS_ENABLED  => Mage::helper('Enterprise_Banner_Helper_Data')->__('Yes'),
                    Enterprise_Banner_Model_Banner::STATUS_DISABLED => Mage::helper('Enterprise_Banner_Helper_Data')->__('No'),
                ),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action options for this grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('banner_id');
        $this->getMassactionBlock()->setFormFieldName('banner');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('Enterprise_Banner_Helper_Data')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('Enterprise_Banner_Helper_Data')->__('Are you sure you want to delete these banners?')
        ));

        return $this;
    }

    /**
     * Grid row URL getter
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getBannerId()));
    }

    /**
     * Define row click callback
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Add store filter
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column  $column
     * @return Enterprise_Banner_Block_Adminhtml_Banner_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getIndex() == 'stores') {
            $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
