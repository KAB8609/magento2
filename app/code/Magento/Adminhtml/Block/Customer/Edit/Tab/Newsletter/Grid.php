<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter queue grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Edit\Tab\Newsletter;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');

        $this->setUseAjax(true);

        $this->setEmptyText(__('No Newsletter Found'));

    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/newsletter', array('_current'=>true));
    }

    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Newsletter\Model\Resource\Queue\Collection */
        $collection = \Mage::getResourceModel('Magento\Newsletter\Model\Resource\Queue\Collection')
            ->addTemplateInfo()
            ->addSubscriberFilter(\Mage::registry('subscriber')->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('queue_id', array(
            'header'    =>  __('ID'),
            'align'     =>  'left',
            'index'     =>  'queue_id',
            'width'     =>  10
        ));

        $this->addColumn('start_at', array(
            'header'    =>  __('Start date'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'queue_start_at',
            'default'   =>  ' ---- '
        ));

        $this->addColumn('finish_at', array(
            'header'    =>  __('End Date'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'queue_finish_at',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));

        $this->addColumn('letter_sent_at', array(
            'header'    =>  __('Receive Date'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'letter_sent_at',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));

        $this->addColumn('template_subject', array(
            'header'    =>  __('Subject'),
            'align'     =>  'center',
            'index'     =>  'template_subject'
        ));

         $this->addColumn('status', array(
            'header'    =>  __('Status'),
            'align'     =>  'center',
            'filter'    =>  '\Magento\Adminhtml\Block\Customer\Edit\Tab\Newsletter\Grid\Filter\Status',
            'index'     => 'queue_status',
            'renderer'  =>  '\Magento\Adminhtml\Block\Customer\Edit\Tab\Newsletter\Grid\Renderer\Status'
        ));

        $this->addColumn('action', array(
            'header'    =>  __('Action'),
            'align'     =>  'center',
            'filter'    =>  false,
            'sortable'  =>  false,
            'renderer'  =>  '\Magento\Adminhtml\Block\Customer\Edit\Tab\Newsletter\Grid\Renderer\Action'
        ));

        return parent::_prepareColumns();
    }

}
