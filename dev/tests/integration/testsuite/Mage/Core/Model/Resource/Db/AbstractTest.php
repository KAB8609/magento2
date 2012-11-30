<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_Db_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource_Db_Abstract
     */
    protected $_model;

    public function setUp()
    {
        $resource = Mage::getObjectManager()->get('Mage_Core_Model_Resource');
        $this->_model = $this->getMockForAbstractClass('Mage_Core_Model_Resource_Db_Abstract',
            array('resource' => $resource)
        );
    }


    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testConstruct()
    {
        $resourceProperty = new ReflectionProperty(get_class($this->_model), '_resources');
        $resourceProperty->setAccessible(true);
        $this->assertInstanceOf('Mage_Core_Model_Resource', $resourceProperty->getValue($this->_model));
    }

    public function testSetMainTable()
    {
        if (!method_exists('ReflectionMethod', 'setAccessible')) {
            $this->markTestSkipped('Test requires ReflectionMethod::setAccessible (PHP 5 >= 5.3.2).');
        }

        $setMainTableMethod = new ReflectionMethod($this->_model, '_setMainTable');
        $setMainTableMethod->setAccessible(true);

        $tableName = $this->_model->getTable('core_website');
        $idFieldName = 'website_id';

        $setMainTableMethod->invoke($this->_model, $tableName);
        $this->assertEquals($tableName, $this->_model->getMainTable());

        $setMainTableMethod->invoke($this->_model, $tableName, $idFieldName);
        $this->assertEquals($tableName, $this->_model->getMainTable());
        $this->assertEquals($idFieldName, $this->_model->getIdFieldName());
    }

    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testGetTableName()
    {
        $tableNameOrig = 'core_website';
        $tableSuffix = 'suffix';
        $tableName = $this->_model->getTable(array($tableNameOrig, $tableSuffix));
        $this->assertEquals('prefix_core_website_suffix', $tableName);
    }
}
