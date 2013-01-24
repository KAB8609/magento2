<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Simple and virtual product review test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_ReviewTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Review product on frontend.</p>
     *
     * @param string $productType
     * @param string $availability
     *
     * @test
     * @dataProvider reviewInfoInProductDetailsDataProvider
     * @TestlinkId TL-MAGE-3469
     */
    public function reviewInfoInProductDetails($productType, $availability)
    {
        $productData = $this->loadDataSet('Product', 'frontend_' . $productType . '_product_details_validation',
                                          array('general_stock_availability' => $availability));
        $this->productHelper()->createProduct($productData, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();
        $this->productHelper()->verifyFrontendProductInfo($productData);
    }

    public function reviewInfoInProductDetailsDataProvider()
    {
        return array(
//            array('simple', 'In Stock'), skipped due to bug MAGETWO-7170
            array('simple', 'Out of Stock'),
//            array('virtual', 'In Stock'), skipped due to bug MAGETWO-7170
            array('virtual', 'Out of Stock')
        );
    }
}
