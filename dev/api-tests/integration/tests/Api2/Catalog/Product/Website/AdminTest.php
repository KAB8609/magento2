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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test product website resource
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Product_Website_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        self::deleteFixture('product', true);
        foreach ($this->getFixture('websitesAssignedToProduct') as $websiteAssignedToProduct) {
            $this->callModelDelete($websiteAssignedToProduct, true);
        }
        foreach ($this->getFixture('stores') as $store) {
            $this->callModelDelete($store, true);
        }
        foreach ($this->getFixture('storeGroups') as $storeGroup) {
            $this->callModelDelete($storeGroup, true);
        }
        foreach ($this->getFixture('categories') as $category) {
            $this->callModelDelete($category, true);
        }
        foreach ($this->getFixture('websitesNotAssignedToProduct') as $websiteNotAssignedToProduct) {
            $this->callModelDelete($websiteNotAssignedToProduct, true);
        }

        parent::tearDown();
    }

    /**
     * Test of retrieve list of asigned websites (GET method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testRetrieveAsignedWebsites()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callGet('products/' . $product->getId() . '/websites');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $websiteIds = $product->getWebsiteIds();
        $product = Mage::getModel('catalog/product')->load($product->getId());
        foreach (self::getFixture('websitesAssignedToProduct') as $website) {
            $this->assertContains($website->getId(), $websiteIds);
        }
    }

    /**
     * Test of a website assignment to a product (with "copy to stores") (POST method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testAssignWebsiteToProductWithCopyToStores()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_pop(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_pop(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $assignedStoredDataId = array_pop($websiteAssignedToProduct->getStoreIds());
        $notAssignedStoredDataId = array_pop($websiteNotAssignedToProduct->getStoreIds());
        $websitesData = array(
            'website_id' => $websiteNotAssignedToProduct->getId(),
            'copy_to_stores' => array(
                array(
                    'store_from' => $assignedStoredDataId,
                    'store_to' => $notAssignedStoredDataId,
                )
            )
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        // Check website
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $this->assertContains($websiteNotAssignedToProduct->getId(), $product->getWebsiteIds());

        // Check stores
        $existStoreData = Mage::getModel('catalog/product')
            ->setStoreId($assignedStoredDataId)
            ->load($product->getId())
            ->toArray();
        unset($existStoreData['store_id']);
        unset($existStoreData['stock_item']['store_id']);

        $newStoreData = Mage::getModel('catalog/product')
            ->setStoreId($notAssignedStoredDataId)
            ->load($product->getId())
            ->toArray();
        unset($newStoreData['store_id']);
        unset($newStoreData['stock_item']['store_id']);

        $this->assertEquals($existStoreData, $newStoreData);
    }

    /**
     * Test of a website assignment to an unavailable product (POST method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testWebsiteAssignmentToUnavailableProduct()
    {
        /* @var $website Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_pop(self::getFixture('websitesNotAssignedToProduct'));

        $websitesData = array(
            'website_id' => $websiteNotAssignedToProduct->getId()
        );
        $restResponse = $this->callPost('products/-1/websites', $websitesData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals($responseData['messages']['error'][0]['message'], 'Product not found #-1.');
    }

    /**
     * Test invalid website id in request body (POST method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testInvalidWebsiteIdInRequestBody()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $websitesData = array(
            'website_id' => 'invalid_id'
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals($responseData['messages']['error'][0]['message'],
            'Invalid value for "website_id" in request.');
    }

    /**
     * Test of a unavailable website assignment to a product (POST method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testUnavailableWebsiteAssignmentToProduct()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $websitesData = array(
            'website_id' => -1
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals($responseData['messages']['error'][0]['message'], 'Website not found #-1.');
    }

    /**
     * Test of an attempt to assign a website that is already assigned to the product (POST method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testAssignWebsiteThatIsAlreadyAssignedToProduct()
    {
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_pop(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $websitesData = array(
            'website_id' => $websiteAssignedToProduct->getId()
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals(
            $responseData['messages']['error'][0]['message'],
            sprintf(
                'Product #%d is already assigned to website #%d',
                $product->getId(),
                $websiteAssignedToProduct->getId()
            )
        );
    }

    /**
     * Test invalid store ids in copy to stores data (POST method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testInvalidStoreIdsInCopyToStoresData()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_pop(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $websitesData = array(
            'website_id' => $websiteNotAssignedToProduct->getId(),
            'copy_to_stores' => array(
                array(
                    'store_from' => 'invalid_id',
                    'store_to' => 'invalid_id',
                )
            )
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals(
            $responseData['messages']['error'][0]['message'],
            sprintf(
                'Invalid value for "store_from" for the website with ID %s.',
                $websiteNotAssignedToProduct->getId()
            )
        );
        $this->assertEquals(
            $responseData['messages']['error'][1]['message'],
            sprintf(
                'Invalid value for "store_to" for the website with ID %s.',
                $websiteNotAssignedToProduct->getId()
            )
        );
    }

    /**
     * Test use unavailable stores in copy to stores data (POST method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testUseUnavailableStoresInCopyToStoresData()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_pop(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $websitesData = array(
            'website_id' => $websiteNotAssignedToProduct->getId(),
            'copy_to_stores' => array(
                array(
                    'store_from' => -1,
                    'store_to' => -1,
                )
            )
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals(
            $responseData['messages']['error'][0]['message'],
            sprintf(
                'Store not found #-1 for website #%s.',
                $websiteNotAssignedToProduct->getId()
            )
        );
        $this->assertEquals(
            $responseData['messages']['error'][1]['message'],
            sprintf(
                'Store not found #-1 for website #%s.',
                $websiteNotAssignedToProduct->getId()
            )
        );
    }

    /**
     * Test use invalid stores associations in copy to stores data (POST method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testUseInvalidStoresAssociationsInCopyToStoresData()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_pop(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_pop(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $assignedStoredDataId = array_pop($websiteAssignedToProduct->getStoreIds());
        $notAssignedStoredDataId = array_pop($websiteNotAssignedToProduct->getStoreIds());
        $websitesData = array(
            'website_id' => $websiteNotAssignedToProduct->getId(),
            'copy_to_stores' => array(
                array(
                    'store_from' => $notAssignedStoredDataId, // wrong store from which we will copy the information
                    'store_to' => $assignedStoredDataId, // wrong store to which we will copy the information
                )
            )
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $websitesData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals(
            $responseData['messages']['error'][0]['message'],
            sprintf(
                'Store(#%d) from which we will copy the information is not belongs to the product(#%d) being edited.',
                $notAssignedStoredDataId,
                $product->getId()
            )
        );
        $this->assertEquals(
            $responseData['messages']['error'][1]['message'],
            sprintf(
                'Store(#%d) to which we will copy the information is not belongs to the website(#%d) being added.',
                $assignedStoredDataId,
                $websiteNotAssignedToProduct->getId()
            )
        );
    }

    /**
     * Test of a website multi assignment to a product (with copy to stores) (POST method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testMultiAssignWebsiteToProduct()
    {
        $websitesNotAssignedToProduct = self::getFixture('websitesNotAssignedToProduct');
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_pop(self::getFixture('websitesAssignedToProduct'));
        $assignedStoredDataId = array_pop($websiteAssignedToProduct->getStoreIds());
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $multiData = array();
        foreach ($websitesNotAssignedToProduct as $websiteNotAssignedToProduct) {
            $notAssignedStoredDataId = array_pop($websiteNotAssignedToProduct->getStoreIds());
            $multiData[] = array(
                'website_id' => $websiteNotAssignedToProduct->getId(),
                'copy_to_stores' => array(
                    array(
                        'store_from' => $assignedStoredDataId,
                        'store_to' => $notAssignedStoredDataId,
                    )
                )
            );
        }
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $multiData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());


        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($product->getId());

        $existStoreData = Mage::getModel('catalog/product')
            ->setStoreId($assignedStoredDataId)
            ->load($product->getId())
            ->toArray();
        unset($existStoreData['store_id']);
        unset($existStoreData['stock_item']['store_id']);

        foreach ($websitesNotAssignedToProduct as $websiteNotAssignedToProduct) {
            // Check website
            $this->assertContains($websiteNotAssignedToProduct->getId(), $product->getWebsiteIds());

            // Check stores
            $notAssignedStoredDataId = array_pop($websiteNotAssignedToProduct->getStoreIds());
            $newStoreData = Mage::getModel('catalog/product')
                ->setStoreId($notAssignedStoredDataId)
                ->load($product->getId())
                ->toArray();
            unset($newStoreData['store_id']);
            unset($newStoreData['stock_item']['store_id']);

            $this->assertEquals($existStoreData, $newStoreData);
        }
    }

    /**
     * Test of the error representation of a website assignment to a product (POST method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testErrorRepresentationOnMultiAssignWebsiteToProduct()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_pop(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_pop(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $multiData[] = array(
            'website_id' => $websiteNotAssignedToProduct->getId(),
            'copy_to_stores' => array(
                array(
                    'store_from' => 'invalid_id',
                    'store_to' => 'invalid_id',
                )
            )
        );
        $restResponse = $this->callPost('products/' . $product->getId() . '/websites', $multiData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals($responseData['error'][0]['message'],
            sprintf(
                'Invalid value for "store_from" for the website with ID %s.',
                $websiteNotAssignedToProduct->getId()
            )
        );
        $this->assertEquals($responseData['error'][0]['product_id'], $product->getId());
        $this->assertEquals($responseData['error'][0]['website_id'], $websiteNotAssignedToProduct->getId());
    }

    /**
     * Test of a website unassignment from a product (DELETE method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testWebsiteUnassignmentFromProduct()
    {
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_pop(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callDelete('products/' . $product->getId() . '/websites/'
            . $websiteAssignedToProduct->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $this->assertNotContains($websiteAssignedToProduct->getId(), $product->getWebsiteIds());
    }

    /**
     * Test of a unavailable website unassignment from a product (DELETE method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testUnavailableWebsiteUnassignmentFromProduct()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callDelete('products/' . $product->getId() . '/websites/invalid_website_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals($responseData['messages']['error'][0]['message'], 'Website not found #invalid_website_id.');
    }

    /**
     * Test of a website unassignment from an unavailable product (DELETE method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testWebsiteUnassignmentFromUnavailableProduct()
    {
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_pop(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callDelete('products/product_invalid_id/websites/' . $websiteAssignedToProduct->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals($responseData['messages']['error'][0]['message'], 'Product not found #product_invalid_id.');
    }

    /**
     * Test of an attempt to unassign a website that is not assigned to a product (DELETE method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testUnassignWebsiteThatIsNotAssignedToProduct()
    {
        /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
        $websiteNotAssignedToProduct = array_pop(self::getFixture('websitesNotAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callDelete('products/' . $product->getId() . '/websites/'
            . $websiteNotAssignedToProduct->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals(
            $responseData['messages']['error'][0]['message'],
            sprintf(
                'Product #%d isn\'t assigned to website #%d',
                $product->getId(),
                $websiteNotAssignedToProduct->getId()
            )
        );
    }

    /**
     * Test of retrieve an assigned website (GET method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testRetrieveSingleAssignedWebsite()
    {
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_pop(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callGet('products/' . $product->getId() . '/websites/'
            . $websiteAssignedToProduct->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $restResponse->getStatus());
    }

    /**
     * Test of update an assigned website (PUT method)
     *
     * @magentoDataFixture Api2/Catalog/Product/Website/_fixtures/for_websites.php
     */
    public function testUpdateAssignedWebsite()
    {
        /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
        $websiteAssignedToProduct = array_pop(self::getFixture('websitesAssignedToProduct'));
        /* @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product');

        $restResponse = $this->callPut('products/' . $product->getId() . '/websites/'
            . $websiteAssignedToProduct->getId(), array('somedata'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $restResponse->getStatus());
    }
}
