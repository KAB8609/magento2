<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Directory_Model_Resource_Country_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Directory_Model_Resource_Country_Collection
     */
    protected $_model;

    protected function setUp()
    {
        $connection = $this->getMock('Magento_DB_Adapter_Pdo_Mysql', array(), array(), '', false);
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $connection->expects($this->once())
            ->method('select')
            ->will($this->returnValue($select));

        $resource = $this->getMockForAbstractClass('Magento_Core_Model_Resource_Db_Abstract', array(), '', false, true,
            true, array('getReadConnection', 'getMainTable', 'getTable'));
        $resource->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($connection));
        $resource->expects($this->any())
            ->method('getTable')
            ->will($this->returnArgument(0));

        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $stringHelper = $this->getMock('Magento_Core_Helper_String', array(), array(), '', false);
        $localeMock = $this->getMock('Magento_Core_Model_LocaleInterface');
        $localeMock->expects($this->any())->method('getCountryTranslation')->will($this->returnArgument(0));

        $fetchStrategy = $this->getMockForAbstractClass('Magento_Data_Collection_Db_FetchStrategyInterface');
        $entityFactory = $this->getMock('Magento_Core_Model_EntityFactory', array(), array(), '', false);
        $storeConfigMock = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        $countryFactory = $this->getMock('Magento_Directory_Model_Resource_CountryFactory',
            array(), array(), '', false);
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = array(
            'logger' => $logger,
            'eventManager' => $eventManager,
            'stringHelper' => $stringHelper,
            'locale' => $localeMock,
            'fetchStrategy' => $fetchStrategy,
            'entityFactory' => $entityFactory,
            'coreStoreConfig' => $storeConfigMock,
            'countryFactory' => $countryFactory,
            'resource' => $resource,
        );
        $this->_model = $objectManager->getObject('Magento_Directory_Model_Resource_Country_Collection', $arguments);
    }

    /**
     * @dataProvider toOptionArrayDataProvider
     * @param array $optionsArray
     * @param string|boolean $emptyLabel
     * @param string|array $foregroundCountries
     * @param array $expectedResults
     */
    public function testToOptionArray($optionsArray, $emptyLabel, $foregroundCountries, $expectedResults)
    {
        foreach ($optionsArray as $itemData) {
            $this->_model->addItem(new Magento_Object($itemData));
        }

        $this->_model->setForegroundCountries($foregroundCountries);
        $result = $this->_model->toOptionArray($emptyLabel);
        $this->assertEquals(count($optionsArray) + (int)!empty($emptyLabel), count($result));
        foreach ($expectedResults as $index => $expectedResult) {
            $this->assertEquals($expectedResult, $result[$index]['label']);
        }
    }

    /**
     * @return array
     */
    public function toOptionArrayDataProvider()
    {
        $optionsArray = array(
            array('iso2_code' => 'AD', 'country_id' => 'AD', 'name' => ''),
            array('iso2_code' => 'US', 'country_id' => 'US', 'name' => ''),
            array('iso2_code' => 'ES', 'country_id' => 'ES', 'name' => ''),
            array('iso2_code' => 'BZ', 'country_id' => 'BZ', 'name' => ''),
        );
        return array(
            array($optionsArray, false, array(), array('AD', 'US', 'ES', 'BZ')),
            array($optionsArray, false, 'US', array('US', 'AD', 'ES', 'BZ')),
            array($optionsArray, false, array('US', 'BZ'), array('US', 'BZ', 'AD', 'ES')),
            array($optionsArray, ' ', 'US', array(' ', 'US', 'AD', 'ES', 'BZ')),
        );
    }
}
