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
 * Adminhtml new accounts report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Accounts_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridAccounts');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/accounts_collection');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('accounts', array(
            'header'    =>Mage::helper('reports')->__('Number of New Accounts'),
            'index'     =>'accounts',
            'total'     =>'sum'
        ));
        /*
        $this->addColumn('items', array(
            'header'    =>Mage::helper('reports')->__('Items Ordered'),
            'index'     =>'items',
            'total'     =>'sum'
        ));

        $currency_code = (string) Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('subtotal', array(
            'header'    =>Mage::helper('reports')->__('Subtotal'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'subtotal',
            'total'     =>'sum'
        ));

        $this->addColumn('tax', array(
            'header'    =>Mage::helper('reports')->__('Tax'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'tax',
            'total'     =>'sum'
        ));

        $this->addColumn('shipping', array(
            'header'    =>Mage::helper('reports')->__('Shipping'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'shipping',
            'total'     =>'sum'
        ));

        $this->addColumn('discount', array(
            'header'    =>Mage::helper('reports')->__('Discounts'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'discount',
            'total'     =>'sum'
        ));

        $this->addColumn('total', array(
            'header'    =>Mage::helper('reports')->__('Total'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'total',
            'total'     =>'sum'
        ));

        $this->addColumn('invoiced', array(
            'header'    =>Mage::helper('reports')->__('Invoiced'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'invoiced',
            'total'     =>'sum'
        ));

        $this->addColumn('refunded', array(
            'header'    =>Mage::helper('reports')->__('Refunded'),
            'type'      =>'currency',
            'currency_code' => $currency_code,
            'index'     =>'refunded',
            'total'     =>'sum'
        ));
        */
        $this->addExportType('*/*/exportAccountsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportAccountsExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}