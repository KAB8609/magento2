<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Rma
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Rma_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getReturnAddressDataProvider
     */
    public function testGetReturnAddressData($useStoreAddress, $storeConfigData, $mockConfig, $expectedResult)
    {
        $storeConfigMock = $this->getMock('Mage_Core_Model_Store_Config', array(), array(), '', false);
        $storeConfigMock->expects($this->any())
            ->method('getConfigFlag')
            ->with(Enterprise_Rma_Model_Rma::XML_PATH_USE_STORE_ADDRESS, $mockConfig['store_id'])
            ->will($this->returnValue($useStoreAddress));

        $storeConfigMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValueMap($storeConfigData));

        $model = new Enterprise_Rma_Helper_Data(
            $this->_getAppMock($mockConfig),
            $storeConfigMock,
            $this->_getCountryFactoryMock($mockConfig),
            $this->_getRegionFactoryMock($mockConfig)
        );
        $this->assertEquals($model->getReturnAddressData(), $expectedResult);
    }

    /**
     * Create application mock
     *
     * @param array $mockConfig
     * @return Mage_Core_Model_App|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAppMock($mockConfig)
    {
        $appMock = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $appMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($mockConfig['store_id']));
        return $appMock;
    }

    /**
     * Create country factory mock
     *
     * @param array $mockConfig
     * @return Mage_Directory_Model_Country|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCountryFactoryMock(array $mockConfig)
    {
        $countryMock = $this->getMock('Mage_Directory_Model_Country', array(), array(), '', false);
        $countryMock->expects($this->any())
            ->method('loadByCode')
            ->will($this->returnValue($countryMock));
        $countryMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($mockConfig['country_name']));
        $countryFactoryMock = $this->getMock('Mage_Directory_Model_CountryFactory', array(), array(), '', false);
        $countryFactoryMock->expects($this->any())->method('create')->will($this->returnValue($countryMock));

        return $countryFactoryMock;
    }

    /**
     * Create region factory mock
     *
     * @param array $mockConfig
     * @return Mage_Directory_Model_Region|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getRegionFactoryMock(array $mockConfig)
    {
        $regionMock = $this->getMock(
            'Mage_Directory_Model_Region',
            array('load', 'getCode', 'getName'),
            array(),
            '',
            false
        );
        $regionMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($regionMock));
        $regionMock->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue($mockConfig['region_id']));
        $regionMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($mockConfig['region_name']));
        $regionFactoryMock = $this->getMock('Mage_Directory_Model_RegionFactory', array(), array(), '', false);
        $regionFactoryMock->expects($this->any())->method('create')->will($this->returnValue($regionMock));

        return $regionFactoryMock;
    }

    public function getReturnAddressDataProvider()
    {
        return array(
            array(
                true,
                array(
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_CITY, 1, 'Kabul'),
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID, 1, 'AF'),
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP, 1, '912232'),
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_REGION_ID, 1, 'Kabul'),
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS2, 1, 'Test Street 2'),
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS1, 1, 'Test Street 1'),
                ),
                array(
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul',
                ),
                array(
                    'city' => 'Kabul',
                    'countryId' => 'AF',
                    'postcode' => '912232',
                    'region_id' => 'Kabul',
                    'street2' => 'Test Street 2',
                    'street1' => 'Test Street 1',
                    'country' => 'Afghanistan',
                    'region' => 'Kabul',
                    'company' => null,
                    'telephone' => null
                )
            ),
            array(
                false,
                array(
                    array(Enterprise_Rma_Model_Shipping::XML_PATH_CITY, 1, 'Kabul'),
                    array(Enterprise_Rma_Model_Shipping::XML_PATH_COUNTRY_ID, 1, 'AF'),
                    array(Enterprise_Rma_Model_Shipping::XML_PATH_ZIP, 1, '912232'),
                    array(Enterprise_Rma_Model_Shipping::XML_PATH_REGION_ID, 1, 'Kabul'),
                    array(Enterprise_Rma_Model_Shipping::XML_PATH_ADDRESS2, 1, 'Test Street 2'),
                    array(Enterprise_Rma_Model_Shipping::XML_PATH_ADDRESS1, 1, 'Test Street 1'),
                ),
                array(
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul',
                ),
                array(
                    'city' => 'Kabul',
                    'countryId' => 'AF',
                    'postcode' => '912232',
                    'region_id' => 'Kabul',
                    'street2' => 'Test Street 2',
                    'street1' => 'Test Street 1',
                    'country' => 'Afghanistan',
                    'region' => 'Kabul',
                    'company' => null,
                    'telephone' => null
                )
            ),
            // Test Case which checks that country name is an empty string for wrong country_id
            array(
                true,
                array(
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_CITY, 1, 'Kabul'),
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID, 1, null),
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP, 1, '912232'),
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_REGION_ID, 1, 'Kabul'),
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS2, 1, 'Test Street 2'),
                    array(Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS1, 1, 'Test Street 1'),
                ),
                array(
                    'store_id' => 1,
                    'country_name' => 'Afghanistan',
                    'region_name' => 'Kabul',
                    'region_id' => 'Kabul',
                ),
                array(
                    'city' => 'Kabul',
                    'countryId' => null,
                    'postcode' => '912232',
                    'region_id' => 'Kabul',
                    'street2' => 'Test Street 2',
                    'street1' => 'Test Street 1',
                    'country' => '',
                    'region' => 'Kabul',
                    'company' => null,
                    'telephone' => null
                )
            ),
        );
    }
}
