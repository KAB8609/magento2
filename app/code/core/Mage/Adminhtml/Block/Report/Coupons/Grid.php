<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml coupons report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Coupons_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridCoupons');
        $this->setSubReportSize(false);
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/coupons_collection');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('coupon_code', array(
            'header'    => $this->__('Coupon Code'),
            'sortable'  => false,
            'index'     => 'coupon_code'
        ));

        $this->addColumn('uses', array(
            'header'    => $this->__('Number of Use'),
            'sortable'  => false,
            'index'     => 'uses',
            'total'     => 'sum'
        ));

        $this->addColumn('discount_amount', array(
            'header'        => $this->__('Discount Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'         => 'discount',
            'total'         => 'sum'
        ));

        $this->addColumn('total_amount', array(
            'header'        => $this->__('Total Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'         => 'total',
            'total'         => 'sum'
        ));

        $this->addExportType('*/*/exportOrdersCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportTotalsExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}