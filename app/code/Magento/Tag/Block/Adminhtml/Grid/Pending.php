<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml pending tags grid
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Grid_Pending extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pending_grid')
             ->setDefaultSort('name')
             ->setDefaultDir('ASC')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Magento_Tag_Model_Resource_Tag_Collection')
            ->addSummary(0)
            ->addStoresVisibility()
            ->addStatusFilter(Magento_Tag_Model_Tag::STATUS_PENDING);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('name', array(
            'header'        => Mage::helper('Magento_Tag_Helper_Data')->__('Tag'),
            'index'         => 'name'
        ));

        $this->addColumn('products', array(
            'header'        => Mage::helper('Magento_Tag_Helper_Data')->__('Products'),
            'width'         => '140px',
            'align'         => 'right',
            'index'         => 'products',
            'type'          => 'number'
        ));

        $this->addColumn('customers', array(
            'header'        => Mage::helper('Magento_Tag_Helper_Data')->__('Customers'),
            'width'         => '140px',
            'align'         => 'right',
            'index'         => 'customers',
            'type'          => 'number'
        ));

        // Collection for stores filters
        if (!$collection = Mage::registry('stores_select_collection')) {
            $collection =  Mage::app()->getStore()->getResourceCollection()
                ->load();
            Mage::register('stores_select_collection', $collection);
        }

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'    => Mage::helper('Magento_Tag_Helper_Data')->__('Store View'),
                'type'      => 'store',
                'index'     => 'stores',
                'sortable'  => false,
                'store_view'=> true
            ));
        }

        return parent::_prepareColumns();
    }

    /**
     * Retrives row click URL
     *
     * @param  mixed $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('tag_id' => $row->getId(), 'ret' => 'pending'));
    }

    protected function _addColumnFilterToCollection($column)
    {
        if($column->getIndex() == 'stores') {
            $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('tag_id');
        $this->getMassactionBlock()->setFormFieldName('tag');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('Magento_Tag_Helper_Data')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete', array('ret' => 'pending')),
             'confirm' => Mage::helper('Magento_Tag_Helper_Data')->__('Are you sure?')
        ));

        $statuses = $this->helper('Magento_Tag_Helper_Data')->getStatusesOptionsArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));

        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('Magento_Tag_Helper_Data')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true, 'ret' => 'pending')),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('Magento_Tag_Helper_Data')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        return $this;
    }

    /*
     * Retrieves Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/tag/ajaxPendingGrid', array('_current' => true));
    }
}
