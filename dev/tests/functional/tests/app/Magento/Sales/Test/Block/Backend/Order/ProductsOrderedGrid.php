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

namespace Magento\Sales\Test\Block\Backend\Order;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

/**
 * Grid for products already present in order during it creation in backend
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class ProductsOrderedGrid extends Grid
{
    /**
     * Click add new products button
     */
    public function addNewProducts()
    {
        $this->_rootElement->find('//button/span[.="Add Products"]', Locator::SELECTOR_XPATH)->click();
    }
}
