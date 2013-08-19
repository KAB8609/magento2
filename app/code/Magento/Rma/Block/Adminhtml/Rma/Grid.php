<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Grid
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('rmaGrid');
        $this->setDefaultSort('date_requested');
        $this->setDefaultDir('DESC');
    }

    /**
     * Prepare related item collection
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _prepareCollection()
    {
        $this->_beforePrepareCollection();
        return parent::_prepareCollection();
    }

    /**
     * Configuring and setting collection
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _beforePrepareCollection()
    {
        if (!$this->getCollection()) {
            $collection = Mage::getResourceModel('Magento_Rma_Model_Resource_Rma_Grid_Collection');
            $this->setCollection($collection);
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('Magento_Rma_Helper_Data')->__('RMA'),
            'type'   => 'number',
            'index'  => 'increment_id',
            'header_css_class'  => 'col-rma-number',
            'column_css_class'  => 'col-rma-number'
        ));

        $this->addColumn('date_requested', array(
            'header' => Mage::helper('Magento_Rma_Helper_Data')->__('Requested Date'),
            'index' => 'date_requested',
            'type' => 'datetime',
            'html_decorators' => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('Magento_Rma_Helper_Data')->__('Order'),
            'type'   => 'number',
            'index'  => 'order_increment_id',
            'header_css_class'  => 'col-order-number',
            'column_css_class'  => 'col-order-number'
        ));

        $this->addColumn('order_date', array(
            'header' => Mage::helper('Magento_Rma_Helper_Data')->__('Order Date'),
            'index' => 'order_date',
            'type' => 'datetime',
            'html_decorators' => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('Magento_Rma_Helper_Data')->__('Customer'),
            'index' => 'customer_name',
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name'
        ));

        $this->addColumn('status', array(
            'header'  => Mage::helper('Magento_Rma_Helper_Data')->__('Status'),
            'index'   => 'status',
            'type'    => 'options',
            'options' => Mage::getModel('Magento_Rma_Model_Rma')->getAllStatuses(),
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('Magento_Rma_Helper_Data')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('Magento_Rma_Helper_Data')->__('View'),
                        'url'       => array('base'=> $this->_getControllerUrl('edit')),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
                'header_css_class'  => 'col-actions',
                'column_css_class'  => 'col-actions'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_ids');

        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('Magento_Rma_Helper_Data')->__('Close'),
            'url'  => $this->getUrl($this->_getControllerUrl('close')),
            'confirm'  => Mage::helper('Magento_Rma_Helper_Data')->__("You have chosen to change status(es) of the selected RMA requests to Close. Are you sure you want to proceed?")
        ));

        return $this;
    }

    /**
     * Get Url to action
     *
     * @param  string $action action Url part
     * @return string
     */
    protected function _getControllerUrl($action = '')
    {
        return '*/*/' . $action;
    }

    /**
     * Retrieve row url
     *
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl($this->_getControllerUrl('edit'), array(
            'id' => $row->getId()
        ));
    }
}
