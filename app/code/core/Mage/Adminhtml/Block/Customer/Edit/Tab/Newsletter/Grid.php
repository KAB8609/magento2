<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter queue grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');

        $this->setUseAjax(true);

        $this->setEmptyText(Mage::helper('Mage_Customer_Helper_Data')->__('No Newsletter Found'));

    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/newsletter', array('_current'=>true));
    }

    protected function _prepareCollection()
    {
        /** @var $collection Mage_Newsletter_Model_Resource_Queue_Collection */
        $collection = Mage::getResourceModel('Mage_Newsletter_Model_Resource_Queue_Collection')
            ->addTemplateInfo()
            ->addSubscriberFilter(Mage::registry('subscriber')->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('queue_id', array(
            'header'    =>  Mage::helper('Mage_Customer_Helper_Data')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'queue_id',
            'width'     =>  10
        ));

        $this->addColumn('start_at', array(
            'header'    =>  Mage::helper('Mage_Customer_Helper_Data')->__('Newsletter Start'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'queue_start_at',
            'default'   =>  ' ---- '
        ));

        $this->addColumn('finish_at', array(
            'header'    =>  Mage::helper('Mage_Customer_Helper_Data')->__('Newsletter Finish'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'queue_finish_at',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));

        $this->addColumn('letter_sent_at', array(
            'header'    =>  Mage::helper('Mage_Customer_Helper_Data')->__('Newsletter Received'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'letter_sent_at',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));

        $this->addColumn('template_subject', array(
            'header'    =>  Mage::helper('Mage_Customer_Helper_Data')->__('Subject'),
            'align'     =>  'center',
            'index'     =>  'template_subject'
        ));

         $this->addColumn('status', array(
            'header'    =>  Mage::helper('Mage_Customer_Helper_Data')->__('Status'),
            'align'     =>  'center',
            'filter'    =>  'Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Filter_Status',
            'index'     => 'queue_status',
            'renderer'  =>  'Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Renderer_Status'
        ));

        $this->addColumn('action', array(
            'header'    =>  Mage::helper('Mage_Customer_Helper_Data')->__('Action'),
            'align'     =>  'center',
            'filter'    =>  false,
            'sortable'  =>  false,
            'renderer'  =>  'Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Renderer_Action'
        ));

        return parent::_prepareColumns();
    }

}
