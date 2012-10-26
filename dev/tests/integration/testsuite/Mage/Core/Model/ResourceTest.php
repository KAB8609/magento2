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
}
