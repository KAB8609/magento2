<?php
/**
 * API Resource config integration tests.
 *
 * @copyright {}
 */

/**#@+
 * Data structures should be available without auto loader as the file name cannot be calculated from class name.
 */
include __DIR__ . '/../../../_files/Model/Webapi/ModuleA/ModuleAData.php';
include __DIR__ . '/../../../_files/Model/Webapi/ModuleA/ModuleADataB.php';
include __DIR__ . '/../../../_files/Controller/Webapi/ModuleA.php';
include __DIR__ . '/../../../_files/Controller/Webapi/SubresourceB.php';
/**#@-*/

/**
 * Class for {@see Mage_Webapi_Model_Config} model testing.
 *
 * The main purpose of this test case is to check config data structure after initialization.
 */
class Mage_Webapi_Model_Config_Soap_DataTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Model_Config_Soap */
    protected $_config;

    /**
     * Set up config with fixture controllers directory scanner
     */
    protected function setUp()
    {
        $fixtureDir = __DIR__ . '/../../../_files/Controller/Webapi/';
        $directoryScanner = new \Zend\Code\Scanner\DirectoryScanner($fixtureDir);
        /** @var Mage_Core_Model_Cache $cache */
        $cache = $this->getMockBuilder('Mage_Core_Model_Cache')->disableOriginalConstructor()->getMock();
        /** @var Mage_Core_Model_App $app */
        $app = $this->getMockBuilder('Mage_Core_Model_App')->disableOriginalConstructor()->getMock();
        $appConfig = Mage::app()->getConfig();
        $objectManager = new Magento_Test_ObjectManager();
        /** @var Mage_Webapi_Helper_Data $helper */
        $helper = $objectManager->get('Mage_Webapi_Helper_Data');
        /** @var Mage_Webapi_Model_Config_Reader_Soap_ClassReflector $classReflector */
        $classReflector = $objectManager->get('Mage_Webapi_Model_Config_Reader_Soap_ClassReflector');
        $reader = new Mage_Webapi_Model_Config_Reader_Soap($classReflector, $helper, $appConfig, $cache);
        $reader->setDirectoryScanner($directoryScanner);

        $this->_config = new Mage_Webapi_Model_Config_Soap($reader, $helper, $app);
        $objectManager->addSharedInstance($this->_config, 'Mage_Webapi_Model_Config_Soap');
    }


    /**
     * Test getResourceDataMerged() functionality.
     * Expected result of method is placed in file fixture.
     */
    public function testGetResource()
    {
        $expectedResourceA = include __DIR__ . '/../../../_files/config/resource_a_fixture.php';
        $this->assertEquals($expectedResourceA, $this->_config->getResourceDataMerged('namespaceAModuleA', 'v1'),
            'Version 1 resource_a data does not match');

        $this->assertEquals(
            include __DIR__ . '/../../../_files/config/resource_a_fixture_v2.php',
            $this->_config->getResourceDataMerged('namespaceAModuleA', 'v2'),
            'Version 2 resource_a data does not match.'
        );

        $this->assertEquals(
            include __DIR__ . '/../../../_files/config/resource_a_subresource_b_fixture.php',
            $this->_config->getResourceDataMerged('namespaceAModuleASubresourceB', 'v1'),
            'Version 1 resource_a_subresource_b data does no match.'
        );
    }

    /**
     * Test getDataType functionality.
     * Expected result of method is placed in file fixture.
     */
    public function testGetDataType()
    {
        $expectedType = include __DIR__ . '/../../../_files/config/data_structure_fixture.php';
        $this->assertEquals($expectedType, $this->_config->getTypeData('NamespaceAModuleAData'));
    }
}
