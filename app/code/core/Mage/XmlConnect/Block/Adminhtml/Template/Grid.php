<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect AirMail message queue grid
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Setting grid_id, sort order and sort direction
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('app_template_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Setting collection to show
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Mage_XmlConnect_Model_Template')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('template_id', array(
            'header'    => $this->__('ID'),
            'align'     => 'center',
            'index'     => 'template_id',
            'width'     => '40px'
        ));

        $this->addColumn('name', array(
            'header'    => $this->__('Template Name'),
            'align'     => 'left',
            'index'     => 'main_table.name',
            'renderer'  => 'Mage_XmlConnect_Block_Adminhtml_Template_Grid_Renderer_Name',
            'escape'    => true
        ));

        $this->addColumn('created_at', array(
            'header'    => $this->__('Date Created'),
            'align'     => 'left',
            'index'     => 'created_at',
            'type'      => 'datetime'
        ));

        $this->addColumn('modified_at', array(
            'header'    => $this->__('Date Updated'),
            'align'     => 'left',
            'index'     => 'modified_at',
            'type'      => 'datetime'
        ));

        $this->addColumn('app_code', array(
            'header'    => $this->__('Application'),
            'index'     => 'app.code',
            'type'      => 'options',
            'align'     => 'left',
            'options'   => Mage::helper('Mage_XmlConnect_Helper_Data')->getApplications(),
            'renderer'  => 'Mage_XmlConnect_Block_Adminhtml_Template_Grid_Renderer_Application',
            'escape'    => true
        ));

        $this->addColumn('push_title', array(
            'header'    => $this->__('Push Title'),
            'type'      => 'text',
            'align'     => 'left',
            'index'     => 'push_title',
            'escape'    => true
        ));

        $this->addColumn('message_title', array(
            'header'    => $this->__('Message Title'),
            'type'      => 'text',
            'align'     => 'left',
            'index'     => 'message_title',
            'escape'    => true
        ));

        $this->addColumn('action', array(
            'header'    => $this->__('Action'),
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => $this->__('Preview'),
                    'url'       => array(
                        'base' => '*/*/previewTemplate'
                    ),
                    'popup'     => true,
                    'field'     => 'id'
                ),
                array(
                    'caption'   => $this->__('Queue Message'),
                    'url'       => array(
                        'base' => '*/*/queueMessage',
                    ),
                    'field'     => 'template_id'
                ),
            ),
            'filter'    => false,
            'sortable'  => false,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Configure row click url
     *
     * @param Mage_Catalog_Model_Template|Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/editTemplate', array('id' => $row->getId()));
    }
}
