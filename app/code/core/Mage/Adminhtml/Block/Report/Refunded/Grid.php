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
 * Adminhtml refunded report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Refunded_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridRefunded');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/refunded_collection');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('orders', array(
            'header'    =>Mage::helper('reports')->__('Number of Orders'),
            'index'     =>'orders',
            'total'     =>'sum'
        ));

        $this->addColumn('online_refunded', array(
            'header'    =>Mage::helper('reports')->__('Online Refunded'),
            'type'      =>'currency',
            'currency_code'=>(string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'online_refunded',
            'total'     =>'sum'
        ));

        $this->addColumn('offline_refunded', array(
            'header'    =>Mage::helper('reports')->__('Offline Refunded'),
            'type'      =>'currency',
            'currency_code'=>(string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode(),
            'index'     =>'offline_refunded',
            'total'     =>'sum'
        ));

        $this->addExportType('*/*/exportRefundedCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportRefundedExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}