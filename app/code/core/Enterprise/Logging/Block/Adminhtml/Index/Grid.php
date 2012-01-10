<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Actions Log Grid
 */
class Enterprise_Logging_Block_Adminhtml_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('loggingLogGrid');
        $this->setDefaultSort('time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * PrepareCollection method.
     */
    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getResourceModel('Enterprise_Logging_Model_Resource_Event_Collection'));
        return parent::_prepareCollection();
    }

    /**
     * Return grids url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Grid URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/details', array('event_id'=>$row->getId()));
    }

    /**
     * Configuration of grid
     *
     * @return Enterprise_Logging_Block_Adminhtml_Index_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('time', array(
            'header'    => Mage::helper('Enterprise_Logging_Helper_Data')->__('Time'),
            'index'     => 'time',
            'type'      => 'datetime',
            'width'     => 160,
        ));

        $this->addColumn('event', array(
            'header'    => Mage::helper('Enterprise_Logging_Helper_Data')->__('Action Group'),
            'index'     => 'event_code',
            'type'      => 'options',
            'sortable'  => false,
            'options'   => Mage::getSingleton('Enterprise_Logging_Model_Config')->getLabels(),
        ));

        $actions = array();
        $fieldValues = Mage::getResourceSingleton('Enterprise_Logging_Model_Resource_Event')
            ->getAllFieldValues('action');
        foreach ($fieldValues as $action) {
            $actions[$action] = Mage::helper('enterprise_logging')->getLoggingActionTranslatedLabel($action);
        }
        $this->addColumn('action', array(
            'header'    => Mage::helper('Enterprise_Logging_Helper_Data')->__('Action'),
            'index'     => 'action',
            'type'      => 'options',
            'options'   => $actions,
            'sortable'  => false,
            'width'     => 75,
        ));

        $this->addColumn('ip', array(
            'header'    => Mage::helper('Enterprise_Logging_Helper_Data')->__('IP Address'),
            'index'     => 'ip',
            'type'      => 'text',
            'filter'    => 'Enterprise_Logging_Block_Adminhtml_Grid_Filter_Ip',
            'renderer'  => 'Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Ip',
            'sortable'  => false,
            'width'     => 125,
        ));

        $this->addColumn('user', array(
            'header'    => Mage::helper('Enterprise_Logging_Helper_Data')->__('Username'),
            'index'     => 'user',
            'type'      => 'text',
            'escape'    => true,
            'sortable'  => false,
            'filter'    => 'Enterprise_Logging_Block_Adminhtml_Grid_Filter_User',
            'width'     => 150,
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('Enterprise_Logging_Helper_Data')->__('Result'),
            'index'     => 'status',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => array(
                Enterprise_Logging_Model_Event::RESULT_SUCCESS => Mage::helper('Enterprise_Logging_Helper_Data')->__('Success'),
                Enterprise_Logging_Model_Event::RESULT_FAILURE => Mage::helper('Enterprise_Logging_Helper_Data')->__('Failure'),
            ),
            'width'     => 100,
        ));

        $this->addColumn('fullaction', array(
            'header'   => Mage::helper('Enterprise_Logging_Helper_Data')->__('Full Action Name'),
            'index'    => 'fullaction',
            'sortable' => false,
            'type'     => 'text'
        ));

        $this->addColumn('info', array(
            'header'    => Mage::helper('Enterprise_Logging_Helper_Data')->__('Short Details'),
            'index'     => 'info',
            'type'      => 'text',
            'sortable'  => false,
            'filter'    => 'Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text',
            'renderer'  => 'Enterprise_Logging_Block_Adminhtml_Grid_Renderer_Details',
            'width'     => 100,
        ));

        $this->addColumn('view', array(
            'header'  => Mage::helper('Enterprise_Logging_Helper_Data')->__('Full Details'),
            'width'   => 50,
            'type'    => 'action',
            'getter'  => 'getId',
            'actions' => array(array(
                'caption' => Mage::helper('Enterprise_Logging_Helper_Data')->__('View'),
                'url'     => array(
                    'base'   => '*/*/details',
                ),
                'field'   => 'event_id'
            )),
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('Mage_Customer_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('Mage_Customer_Helper_Data')->__('Excel XML'));
        return $this;
    }


}