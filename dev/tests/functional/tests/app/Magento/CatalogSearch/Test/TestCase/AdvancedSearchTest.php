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

namespace Magento\CatalogSearch\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class AdvancedSearchTest
 * Searching product in the Frontend via advanced search
 *
 * @package Magento\CatalogSearch\Test\TestCase
 */
class AdvancedSearchTest extends Functional
{
    /**
     * Advanced search product on frontend by product name
     *
     * @ZephyrId MAGETWO-12421
     */
    public function testProductSearch()
    {
        //Data
        $productFixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $productFixture->switchData('simple');
        $productFixture->persist();

        //Pages
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $advancedSearchPage = Factory::getPageFactory()->getCatalogsearchAdvanced();
        $advancedSearchResultPage = Factory::getPageFactory()->getCatalogsearchAdvancedResult();

        //Steps
        $homePage->open();
        $homePage->getFooterBlock()->clickLink('Advanced Search');
        $searchForm = $advancedSearchPage->getSearchForm();
        $this->assertTrue($searchForm->isVisible(), '"Advanced Search" form is not opened');
        $searchForm->fillCustom($productFixture, array('name', 'sku'));
        $searchForm->submit();

        //Verifying
        $productName = $productFixture->getProductName();
        $this->assertTrue(
            $advancedSearchResultPage->getListProductBlock()->isProductVisible($productName),
            sprintf('Product "%s" is not displayed on the "Catalog Advanced Search" results page."', $productName)
        );
    }
}