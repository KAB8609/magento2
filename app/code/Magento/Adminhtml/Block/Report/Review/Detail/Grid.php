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
 * Adminhtml report reviews product grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Report\Review\Detail;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * @var \Magento\Reports\Model\Resource\Review\CollectionFactory
     */
    protected $_reviewsFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Reports\Model\Resource\Review\CollectionFactory $reviewsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Reports\Model\Resource\Review\CollectionFactory $reviewsFactory,
        array $data = array()
    ) {
        $this->_reviewsFactory = $reviewsFactory;
        parent::__construct($context, $coreData, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('reviews_grid');
    }

    protected function _prepareCollection()
    {
        $collection = $this->_reviewsFactory->create()
            ->addProductFilter((int)$this->getRequest()->getParam('id'));

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {

        $this->addColumn('nickname', array(
            'header'    =>__('Customer'),
            'width'     =>'100px',
            'index'     =>'nickname'
        ));

        $this->addColumn('title', array(
            'header'    =>__('Title'),
            'width'     =>'150px',
            'index'     =>'title'
        ));

        $this->addColumn('detail', array(
            'header'    =>__('Detail'),
            'index'     =>'detail'
        ));

        $this->addColumn('created_at', array(
            'header'    =>__('Created'),
            'index'     =>'created_at',
            'width'     =>'200px',
            'type'      =>'datetime'
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductDetailCsv', __('CSV'));
        $this->addExportType('*/*/exportProductDetailExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

}

