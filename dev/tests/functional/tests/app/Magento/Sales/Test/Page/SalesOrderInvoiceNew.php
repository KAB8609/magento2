<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Magento\Backend\Test\Block\Sales\Order\Invoice\Totals;
use Magento\Backend\Test\Block\Sales\Order\Invoice\Create\Form;

/**
 * Class SalesOrder
 * Manage orders page
 *
 * @package Magento\Sales\Test\Page
 */
class SalesOrderInvoiceNew extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'sales/order/invoice/new';

    /**
     * Sales order grid
     *
     * @var Form
     */
    private $formBlock;

    /**
     * @var Totals
     */
    private $totalsBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->totalsBlock = Factory::getBlockFactory()->getMagentoBackendSalesOrderInvoiceTotals(
            $this->_browser->find('#invoice_totals')
        );
        $this->formBlock = Factory::getBlockFactory()->getMagentoBackendSalesOrderInvoiceCreateForm(
            $this->_browser->find('#edit_form')
        );
    }

    /**
     * Get sales order grid
     *
     * @return Totals
     */
    public function getInvoiceTotalsBlock()
    {
        return $this->totalsBlock;
    }

    /**
     * Get order actions block
     *
     * @return Form
     */
    public function getFormBlock()
    {
        return $this->formBlock;
    }
}