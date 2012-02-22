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
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @magentoDataFixture Api/Catalog/Product/_fixtures/CustomOption.php
 */
class Api_Catalog_Product_CustomOptionCRUDTest extends Magento_Test_Webservice
{
    /**
     * @var array
     */
    protected static $createdOptionAfter;

    /**
     * Product Custom Option CRUD test
     */
    public function testCustomOptionCRUD()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/CustomOption.xml');
        $customOptions = self::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);
        $store = (string) $customOptionFixture->store;
        $fixtureProductId = Magento_Test_Webservice::getFixture('productData')->getId();

        $createdOptionBefore = $this->call('product_custom_option.list', array(
            'productId' => $fixtureProductId,
            'store' => $store
        ));
        $this->assertEmpty($createdOptionBefore);

        foreach ($customOptions as $option) {
            if (isset($option['additional_fields'])
                and !is_array(reset($option['additional_fields']))) {
                $option['additional_fields'] = array($option['additional_fields']);
            }

            $addedOptionResult = $this->call('product_custom_option.add', array(
                'productId' => $fixtureProductId,
                'data' => $option,
                'store' => $store
            ));
            $this->assertTrue((bool)$addedOptionResult);
        }

        // list
        self::$createdOptionAfter = $this->call('product_custom_option.list', array(
            'productId' => $fixtureProductId,
            'store' => $store
        ));

        $this->assertTrue(is_array(self::$createdOptionAfter));
        $this->assertEquals(count($customOptions), count(self::$createdOptionAfter));

        foreach (self::$createdOptionAfter as $option) {
            $this->assertEquals($customOptions[$option['type']]['title'], $option['title']);
        }

        // update & info
        $updateCounter = 0;
        $customOptionsToUpdate = self::simpleXmlToArray($customOptionFixture->CustomOptionsToUpdate);
        foreach (self::$createdOptionAfter as $option) {
            $optionInfo = $this->call('product_custom_option.info', array(
                'optionId' => $option['option_id']
            ));

            $this->assertTrue(is_array($optionInfo));
            $this->assertGreaterThan(3, count($optionInfo));

            if (isset($customOptionsToUpdate[$option['type']])) {
                $toUpdateValues = $customOptionsToUpdate[$option['type']];
                if (isset($toUpdateValues['additional_fields'])
                    and !is_array(reset($toUpdateValues['additional_fields']))) {
                    $toUpdateValues['additional_fields'] = array($toUpdateValues['additional_fields']);
                }

                $updateOptionResult = $this->call('product_custom_option.update', array(
                    'optionId' => $option['option_id'],
                    'data' => $toUpdateValues
                ));
                $this->assertTrue((bool)$updateOptionResult);
                $updateCounter ++;

                $optionInfoAfterUpdate = $this->call('product_custom_option.info', array(
                    'optionId' => $option['option_id']
                ));

                foreach($toUpdateValues as $key => $value) {
                    if(is_string($value)) {
                        self::assertEquals($value, $optionInfoAfterUpdate[$key]);
                    }
                }

                if (isset($toUpdateValues['additional_fields'])) {
                    $updateAdditionalFields = reset($toUpdateValues['additional_fields']);
                    if (TESTS_WEBSERVICE_TYPE == Magento_Test_Webservice::TYPE_SOAPV2_WSI) {
                        // incorrect in case additional_fields count > 1
                        $actualAdditionalFields = $optionInfoAfterUpdate['additional_fields'];
                    } else {
                        $actualAdditionalFields = reset($optionInfoAfterUpdate['additional_fields']);
                    }
                    foreach ($updateAdditionalFields as $key => $value) {
                        if (is_string($value)) {
                            self::assertEquals($value, $actualAdditionalFields[$key]);
                        }
                    }
                }
            }
        }

        $this->assertCount($updateCounter, $customOptionsToUpdate);
    }

    /**
     * Product Custom Option ::types() method test
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionTypes()
    {
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/CustomOptionTypes.xml');
        $customOptionsTypes = self::simpleXmlToArray($attributeSetFixture);

        $optionTypes = $this->call('product_custom_option.types', array());
        $this->assertEquals($customOptionsTypes['customOptionTypes']['types'], $optionTypes);
    }

    /**
     * Update custom option
     *
     * @param int $optionId
     * @param array $option
     * @param int $store
     *
     * @return boolean
     */
    protected function _updateOption($optionId, $option, $store = null)
    {
        if (isset($option['additional_fields'])
            and !is_array(reset($option['additional_fields']))) {
            $option['additional_fields'] = array($option['additional_fields']);
        }

        return $this->call('product_custom_option.update', array(
            'optionId' => $optionId,
            'data' => $option,
            'store' => $store
        ));
    }

    /**
     * Test option add exception: product_not_exists
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionAddExceptionProductNotExists()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/CustomOption.xml');
        $customOptions = self::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);

        $option = reset($customOptions);
        if (isset($option['additional_fields'])
            and !is_array(reset($option['additional_fields']))) {
            $option['additional_fields'] = array($option['additional_fields']);
        }
        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call('product_custom_option.add', array(
            'productId' => 'invalid_id',
            'data' => $option
        ));
    }

    /**
     * Test option add without additional fields exception: invalid_data
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionAddExceptionAdditionalFieldsNotSet()
    {
        if (TESTS_WEBSERVICE_TYPE == Magento_Test_Webservice::TYPE_SOAPV2_WSI) {
            $e = 'SOAP-ERROR: Encoding: object hasn\'t \'additional_fields\' property';
            $this->markTestSkipped('Soap client fails with fatal error: '.$e);
        }
        $fixtureProductId = Magento_Test_Webservice::getFixture('productData')->getId();
        $customOptionFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/CustomOption.xml');
        $customOptions = self::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);

        $option = reset($customOptions);
        unset($option['additional_fields']);

        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call('product_custom_option.add', array(
            'productId' => $fixtureProductId,
            'data' => $option
        ));
    }

    /**
     * Test option date_time add with store id exception: store_not_exists
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionDateTimeAddExceptionStoreNotExist()
    {
        $fixtureProductId = Magento_Test_Webservice::getFixture('productData')->getId();
        $customOptionFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/CustomOption.xml');
        $customOptions = self::simpleXmlToArray($customOptionFixture->CustomOptionsToAdd);

        $option = reset($customOptions);
        if (isset($option['additional_fields'])
            and !is_array(reset($option['additional_fields']))) {
            $option['additional_fields'] = array($option['additional_fields']);
        }
        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call('product_custom_option.add', array(
            'productId' => $fixtureProductId,
            'data' => $option,
            'store' => 'some_store_name'
        ));
    }

    /**
     * Test product custom options list exception: product_not_exists
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionListExceptionProductNotExists()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/CustomOption.xml');
        $store = (string) $customOptionFixture->store;

        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call('product_custom_option.list', array(
            'productId' => 'unknown_id',
            'store' => $store
        ));
    }

    /**
     * Test product custom options list exception: store_not_exists
     *
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionListExceptionStoreNotExists()
    {
        $fixtureProductId = Magento_Test_Webservice::getFixture('productData')->getId();

        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call('product_custom_option.list', array(
            'productId' => $fixtureProductId,
            'store' => 'unknown_store_name'
        ));
    }

    /**
     * Test option add with invalid type
     *
     * @expectedException DEFAULT_EXCEPTION
     * @depends testCustomOptionCRUD
     */
    public function testCustomOptionUpdateExceptionInvalidType()
    {
        $customOptionFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/CustomOption.xml');

        $customOptionsToUpdate = self::simpleXmlToArray($customOptionFixture->CustomOptionsToUpdate);
        $option = reset(self::$createdOptionAfter);

        $toUpdateValues = $customOptionsToUpdate[$option['type']];
        $toUpdateValues['type'] = 'unknown_type';

        $this->_updateOption($option['option_id'], $toUpdateValues);
    }

    /**
     * Test option remove and exception
     *
     * @expectedException DEFAULT_EXCEPTION
     * @depends testCustomOptionUpdateExceptionInvalidType
     */
    public function testCustomOptionRemove()
    {
        // Remove
        foreach (self::$createdOptionAfter as $option) {
            $removeOptionResult = $this->call('product_custom_option.remove', array(
                'optionId' => $option['option_id']
            ));
            $this->assertTrue((bool)$removeOptionResult);
        }

        // Delete exception test
        $this->call('product_custom_option.remove', array('optionId' => mt_rand(99999, 999999)));
    }
}
