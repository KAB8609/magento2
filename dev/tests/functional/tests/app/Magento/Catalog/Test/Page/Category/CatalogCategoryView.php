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

namespace Magento\Catalog\Test\Page\Category;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class CatalogCategoryView
 * Category page on frontend
 *
 * @package Magento\Catalog\Test\Page\Category
 */
class CatalogCategoryView extends Page
{
    /**
     * URL for category page
     */
    const MCA = 'catalog/category/view';

    /**
     * List of results of product search
     *
     * @var string
     */
    protected $listProductBlock = '.products.wrapper.grid';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get product list block
     *
     * @return \Magento\Catalog\Test\Block\Product\ListProduct
     */
    public function getListProductBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductListProduct(
            $this->_browser->find($this->listProductBlock, Locator::SELECTOR_CSS)
        );
    }
}
