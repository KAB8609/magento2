<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml items in carts report grid block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Adminhtml\Shopcart\Customer;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\Shopcart
{
    /**
     * @var \Magento\Reports\Model\Resource\Customer\CollectionFactory
     */
    protected $_customersFactory;

    /**
     * @param \Magento\Reports\Model\Resource\Customer\CollectionFactory $customersFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\Reports\Model\Resource\Customer\CollectionFactory $customersFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        $this->_customersFactory = $customersFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {
        //TODO: add full name logic
        $collection = $this->_customersFactory->create()
          ->addAttributeToSelect('firstname')
          ->addAttributeToSelect('lastname');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->addCartInfo();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>__('ID'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'entity_id'
        ));

        $this->addColumn('firstname', array(
            'header'    =>__('First Name'),
            'index'     =>'firstname'
        ));

        $this->addColumn('lastname', array(
            'header'    =>__('Last Name'),
            'index'     =>'lastname'
        ));

        $this->addColumn('items', array(
            'header'    =>__('Items in Cart'),
            'width'     =>'70px',
            'sortable'  =>false,
            'align'     =>'right',
            'index'     =>'items'
        ));

        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('total', array(
            'header'    =>__('Total'),
            'width'     =>'70px',
            'sortable'  =>false,
            'type'      =>'currency',
            'align'     =>'right',
            'currency_code' => $currencyCode,
            'index'     =>'total',
            'renderer'  =>'Magento\Reports\Block\Adminhtml\Grid\Column\Renderer\Currency',
            'rate'          => $this->getRate($currencyCode),
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportCustomerCsv', __('CSV'));
        $this->addExportType('*/*/exportCustomerExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

}
