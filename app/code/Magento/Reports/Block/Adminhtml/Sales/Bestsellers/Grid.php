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
 * Adminhtml bestsellers report grid block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Adminhtml\Sales\Bestsellers;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid
{
    protected $_columnGroupBy = 'period';

    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName()
    {
        return 'Magento\Sales\Model\Resource\Report\Bestsellers\Collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'        => __('Interval'),
            'index'         => 'period',
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
            'totals_label'  => __('Total'),
            'html_decorators' => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('product_name', array(
            'header'    => __('Product'),
            'index'     => 'product_name',
            'type'      => 'string',
            'sortable'  => false,
            'header_css_class'  => 'col-product',
            'column_css_class'  => 'col-product'
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('product_price', array(
            'header'        => __('Price'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'product_price',
            'sortable'      => false,
            'rate'          => $this->getRate($currencyCode),
            'header_css_class'  => 'col-price',
            'column_css_class'  => 'col-price'
        ));

        $this->addColumn('qty_ordered', array(
            'header'    => __('Order Quantity'),
            'index'     => 'qty_ordered',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false,
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));


        $this->addExportType('*/*/exportBestsellersCsv', __('CSV'));
        $this->addExportType('*/*/exportBestsellersExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }
}