<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scheduled operation grid
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Controller name
     *
     * @var string
     */
    protected $_controller;

    /**
     * Initialize grid object
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setId('operationGrid');
        $this->_controller = 'adminhtml_scheduled_operation';
        $this->setUseAjax(true);

        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    /**
     * Prepare grid collection object
     *
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Enterprise_ImportExport_Model_Resource_Scheduled_Operation_Collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Grid columns definition
     *
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'        => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Name'),
            'index'         => 'name',
            'type'          => 'text',
            'escape'        => true
        ));

        /** @var $dataModel Enterprise_ImportExport_Model_Scheduled_Operation_Data */
        $dataModel = Mage::getSingleton('Enterprise_ImportExport_Model_Scheduled_Operation_Data');
        $this->addColumn('operation_type', array(
            'header'        => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Operation'),
            'width'         => '30px',
            'index'         => 'operation_type',
            'type'          => 'options',
            'options'       => $dataModel->getOperationsOptionArray()
        ));

        $this->addColumn('entity_type', array(
            'header'        => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Entity type'),
            'index'         => 'entity_type',
            'type'          => 'options',
            'options'       => $dataModel->getEntitiesOptionArray()
        ));

        $this->addColumn('entity_subtype', array(
            'header'        => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Entity subtype'),
            'index'         => 'entity_subtype',
            'type'          => 'options',
            'options'       => $dataModel->getEntitySubtypesOptionArray()
        ));

        $this->addColumn('last_run_date', array(
            'header'        => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Last Run Date'),
            'index'         => 'last_run_date',
            'type'          => 'datetime'
        ));

        $this->addColumn('freq', array(
            'header'        => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Frequency'),
            'index'         => 'freq',
            'type'          => 'options',
            'options'       => $dataModel->getFrequencyOptionArray(),
            'width'         => '100px'
        ));

        $this->addColumn('status', array(
            'header'        => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Status'),
            'index'         => 'status',
            'type'          => 'options',
            'options'       => $dataModel->getStatusesOptionArray()
        ));

        $this->addColumn('is_success', array(
            'header'        => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Last Outcome'),
            'index'         => 'is_success',
            'type'          => 'options',
            'width'         => '200px',
            'options'       => $dataModel->getResultOptionArray()
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Action'),
            'width'     => '50px',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Edit'),
                    'url'     => array(
                        'base'=>'*/*/edit',
                    ),
                    'field'   => 'id'
                ),
                array(
                    'caption' => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Run'),
                    'url'     => array(
                        'base'=> '*/scheduled_operation/cron',
                    ),
                    'field'   => 'operation'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'id',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get row url
     *
     * @param Enterprise_ImportExport_Model_Scheduled_Operation
     * @return string
     */
    public function getRowUrl($operation)
    {
        /** @var $operation Varien_Object */
        return $this->getUrl('*/*/edit', array(
            'id' => $operation->getId(),
        ));
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Prepare batch actions
     *
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('operation');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('Enterprise_ImportExport_Helper_Data')
                ->__('Are you sure you want to delete the selected scheduled imports/exports?')
        ));

        /** @var $statusesObject Enterprise_ImportExport_Model_Scheduled_Operation_Data */
        $statusesObject = Mage::getSingleton('Enterprise_ImportExport_Model_Scheduled_Operation_Data');
        $statuses = $statusesObject->getStatusesOptionArray();
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Change status'),
            'url'  => $this->getUrl('*/*/massChangeStatus', array('_current' => true)),
            'additional' => array(
               'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Status'),
                    'values' => $statuses
                )
             )
        ));

        return $this;
    }
}
