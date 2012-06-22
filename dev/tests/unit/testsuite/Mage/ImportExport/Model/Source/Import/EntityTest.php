<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for entity source model Mage_ImportExport_Model_Source_Import_Entity
 */
class Mage_ImportExport_Model_Source_Import_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested source model
     *
     * @var Mage_ImportExport_Model_Source_Import_Entity
     */
    public static $sourceModel;

    /**
     * Helper registry key
     *
     * @var string
     */
    protected static $_helperKey = '_helper/Mage_ImportExport_Helper_Data';

    /**
     * Helper property
     *
     * @var Mage_ImportExport_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected static $_helper;

    /**
     * Test entity
     *
     * @var array
     */
    protected $_testEntity = array(
        'label' => 'test_label',
        'node'  => 'test_node'
    );

    /**
     * Init source model
     *
     * @static
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$sourceModel = new Mage_ImportExport_Model_Source_Import_Entity();
    }

    /**
     * Unregister source model and helper
     *
     * @static
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        Mage::unregister(self::$_helperKey);
        self::$_helper = null;
        self::$sourceModel = null;
    }

    /**
     * Helper initialization
     *
     * @return Mage_ImportExport_Helper_Data
     */
    protected function _initHelper()
    {
        if (!self::$_helper) {
            self::$_helper = $this->getMock(
                'Mage_ImportExport_Helper_Data',
                array('__')
            );
            self::$_helper->expects($this->any())
                ->method('__')
                ->will($this->returnArgument(0));

            Mage::unregister(self::$_helperKey);
            Mage::register(self::$_helperKey, self::$_helper);
        }
        return self::$_helper;
    }

    /**
     * Mock config
     */
    protected function _mockConfig()
    {
        $configObject = new Mage_Core_Model_Config_Base(new Varien_Simplexml_Element('<config></config>'));
        $configObject->setNode(
            'global/importexport/import_entities/' . $this->_testEntity['node'] . '/model_token',
            'Some_Class'
        );
        $configObject->setNode(
            'global/importexport/import_entities/' . $this->_testEntity['node'] . '/label',
            $this->_testEntity['label']
        );

        $config = new ReflectionProperty('Mage', '_config');
        $config->setAccessible(true);
        $config->setValue(null, $configObject);
    }

    /**
     * Is result variable an correct optional array
     */
    public function testToOptionArray()
    {
        $this->_initHelper();
        $this->_mockConfig();

        $optionalArray = self::$sourceModel->toOptionArray();

        $this->assertInternalType('array', $optionalArray, 'Result variable must be an array.');
        $this->assertCount(2, $optionalArray);

        foreach ($optionalArray as $option) {
            $this->assertArrayHasKey('label', $option, 'Option must have label property.');
            $this->assertArrayHasKey('value', $option, 'Option must have value property.');
        }

        $headerElement = $optionalArray[0];
        $dataElement = $optionalArray[1];

        $this->assertEmpty($headerElement['value'], 'Value must be empty.');
        $this->assertEquals($this->_testEntity['node'], $dataElement['value'], 'Incorrect element value.');
        $this->assertEquals($this->_testEntity['label'], $dataElement['label'], 'Incorrect element label.');
    }
}
