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

namespace Magento\Catalog\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class SearchTest
 * Searching product in the Frontend via quick search
 *
 * @package Magento\Catalog\Test\TestCase
 */
class SearchTest extends Functional
{
    /**
     * Using quick search to find the product
     *
     * @ZephyrId MAGETWO-12420
     */
    public function testSearchProductFromHomePage()
    {
        //Preconditions
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('enable_mysql_search');
        $config->persist();

        //Data
        $productFixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $productFixture->switchData('simple');
        $productFixture->persist();
        $productName = $productFixture->getProductName();
        $productSku = $productFixture->getProductSku();

        //Pages & Blocks
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $resultPage = Factory::getPageFactory()->getCatalogsearchResult();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productListBlock = $resultPage->getListProductBlock();
        $viewBlock = $productPage->getViewBlock();

        //Steps
        $homePage->open();
        $homePage->getSearchBlock()->search($productSku);

        //Verifying
        $this->assertTrue($productListBlock->isProductVisible($productName), 'Product was not found.');
        $productListBlock->openProductViewPage($productName);
        $this->assertEquals($productName, $viewBlock->getProductName(), 'Wrong product page has been opened.');
    }
}
