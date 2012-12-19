<?php
/**
 * Test for Mage_Core_Model_Resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Mage_Core_Model_Resource');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testGetTableName()
    {
        $tablePrefix = 'prefix_';
        $tableSuffix = 'suffix';
        $tableNameOrig = 'core_website';

        $tableName = $this->_model->getTableName(array($tableNameOrig, $tableSuffix));
        $this->assertContains($tablePrefix, $tableName);
        $this->assertContains($tableSuffix, $tableName);
        $this->assertContains($tableNameOrig, $tableName);
    }

    /**
     * Init profiler during creation of DB connect
     */
    public function testProfilerInit()
    {
        $connReadConfig = Mage::getConfig()->getResourceConnectionConfig('core_read');
        $profilerConfig = $connReadConfig->addChild('profiler');
        $profilerConfig->addChild('class', 'Mage_Core_Model_Resource_Db_Profiler');
        $profilerConfig->addChild('enabled', 'true');

        /** @var Zend_Db_Adapter_Abstract $connection */
        $connection = $this->_model->getConnection('core_read');
        /** @var Mage_Core_Model_Resource_Db_Profiler $profiler */
        $profiler = $connection->getProfiler();

        $this->assertInstanceOf('Mage_Core_Model_Resource_Db_Profiler', $profiler);
        $this->assertTrue($profiler->getEnabled());
        $this->assertAttributeEquals((string)$connReadConfig->host, '_host', $profiler);
        $this->assertAttributeEquals((string)$connReadConfig->type, '_type', $profiler);
    }
}
