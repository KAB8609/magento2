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
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for review item API2
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Review_Review_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Remove fixtures
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->deleteFixture('product_simple', true);
        $this->deleteFixture('review', true);

        parent::tearDown();
    }

    /**
     * Test successful retrieving of existing product review
     *
     * @param bool $withStore
     * @param int $storeId
     * @dataProvider providerTestGet
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testGet($withStore, $storeId)
    {
        /** @var $product Mage_Review_Model_Review */
        $review = $this->getFixture('review');
        $params = array();
        if ($withStore) {
            $params['store'] = $storeId;
        }
        $restResponse = $this->callGet('review/' . $review->getId(), $params);

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $reviewOriginalData = $review->getData();
        foreach ($responseData as $field => $value) {
            if (!is_array($value)) {
                $this->assertEquals($reviewOriginalData[$field], $value);
            } else {
                $this->assertEquals(count($reviewOriginalData[$field]), count($value));
            }
        }
    }

    /**
     * Data provider for get test
     *
     * @return array
     */
    public function providerTestGet()
    {
        return array(
            // with store
            array(true, reset(Mage::app()->getWebsite()->getStoreIds())),
            // without store
            array(false, null)
        );
    }

    /**
     * Test retrieving not existing review item
     */
    public function testGetUnavailableResource()
    {
        $restResponse = $this->callGet('review/' . 'invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test retrieving existing product review item using invalid store
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testGetWithInvalidStore()
    {
        // admin store should not be available for customers
        $storeId = 0;
        /** @var $product Mage_Review_Model_Review */
        $review = $this->getFixture('review');
        $restResponse = $this->callGet('review/' . $review->getId(), array('store' => $storeId));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);
        $error = reset($errors);
        $expectedErrorMessage = 'Invalid stores provided';
        $this->assertEquals($error['message'], $expectedErrorMessage);
    }

    /**
     * Test retrieving existing product review using store in which this review is unavailable
     */
    public function testGetWithInappropriateStore()
    {
        $this->markTestIncomplete('Implement after store fixture creation');
    }

    /**
     * Test retrieve of existing not approved review
     *
     * @param int $status
     * @dataProvider providerTestGetWithInappropriateStatus
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testGetWithInappropriateStatus($status)
    {
        /** @var $product Mage_Review_Model_Review */
        $review = $this->getFixture('review');
        $review->setStatusId($status)->save();

        $restResponse = $this->callGet('review/' . $review->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Provider of not eligible for customer review statuses
     *
     * @return array
     */
    public function providerTestGetWithInappropriateStatus()
    {
        return array(
            array(Mage_Review_Model_Review::STATUS_NOT_APPROVED),
            array(Mage_Review_Model_Review::STATUS_PENDING),
        );
    }
}

