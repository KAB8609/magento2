<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Shipment;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Collection\Factory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Sales\Model\Resource\Order\Collection\Factory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Sales\Model\Resource\Order\Collection\Factory $collectionFactory,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $coreData, $urlModel, $data);
    }


    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_shipment_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento\Sales\Model\Resource\Order\Shipment\Grid\Collection';
    }

    /**
     * Prepare and set collection of grid
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare and add columns to grid
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header' => __('Shipment'),
            'index' => 'increment_id',
            'type' => 'text',
            'header_css_class' => 'col-shipment-number',
            'column_css_class' => 'col-shipment-number'
        ));

        $this->addColumn('created_at', array(
            'header' => __('Ship Date'),
            'index' => 'created_at',
            'type' => 'datetime',
            'header_css_class' => 'col-period',
            'column_css_class' => 'col-period'
        ));

        $this->addColumn('order_increment_id', array(
            'header' => __('Order'),
            'index' => 'order_increment_id',
            'type' => 'text',
            'header_css_class' => 'col-order-number',
            'column_css_class' => 'col-order-number'
        ));

        $this->addColumn('order_created_at', array(
            'header' => __('Order Date'),
            'index' => 'order_created_at',
            'type' => 'datetime',
            'header_css_class' => 'col-period',
            'column_css_class' => 'col-period'
        ));

        $this->addColumn('shipping_name', array(
            'header' => __('Ship-to Name'),
            'index' => 'shipping_name',
            'header_css_class' => 'col-memo',
            'column_css_class' => 'col-memo'
        ));

        $this->addColumn('total_qty', array(
            'header' => __('Total Quantity'),
            'index' => 'total_qty',
            'type' => 'number',
            'header_css_class' => 'col-qty',
            'column_css_class' => 'col-qty'
        ));

        $this->addColumn('action',
            array(
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => __('View'),
                        'url' => array('base' => 'sales/shipment/view'),
                        'field' => 'shipment_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
                'header_css_class' => 'col-actions',
                'column_css_class' => 'col-actions'
            ));

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    public function getRowUrl($row)
    {
        if (!$this->_authorization->isAllowed(null)) {
            return false;
        }

        return $this->getUrl('sales/shipment/view',
            array(
                'shipment_id' => $row->getId(),
            )
        );
    }

    /**
     * Prepare and set options for massaction
     *
     * @return \Magento\Sales\Block\Adminhtml\Shipment\Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('shipment_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
            'label' => __('PDF Packing Slips'),
            'url' => $this->getUrl('sales/shipment/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
            'label' => __('Print Shipping Labels'),
            'url' => $this->getUrl('sales/order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    /**
     * Get url of grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/*/*', array('_current' => true));
    }

}