<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class SalesOrderCreateIndex
 * Backend order creation page
 *
 * @package Magento\Sales\Test\Page
 */
class SalesOrderCreateIndex extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'sales/order_create/index';

    /**
     * Grid for customer selection
     *
     * @var string
     */
    protected $customerBlock = '#order-customer-selector';

    /**
     * Block for store view selection
     *
     * @var string
     */
    protected $storeBlock = '#order-store-selector';

    /**
     * Sales order create block
     *
     * @var string
     */
    protected $createBlock = '[id="page:main-container"]';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Getter for customer selection grid
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Customer
     */
    public function getCustomerBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreateCustomer(
            $this->_browser->find($this->customerBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Getter for store view selection
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Store
     */
    public function getStoreBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreateStore(
            $this->_browser->find($this->storeBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get sales order create block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create
     */
    public function getCreateBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreate(
            $this->_browser->find($this->createBlock, Locator::SELECTOR_CSS)
        );
    }
}
