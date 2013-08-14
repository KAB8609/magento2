<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     <package-name>
 * @subpackage  <subpackage-name>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Import Data resource model
 *
 * @magentoDataFixture Magento/ImportExport/_files/import_data.php
 */
class Magento_ImportExport_Model_Resource_Import_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ImportExport_Model_Resource_Import_Data
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        $this->_model = Mage::getResourceModel('Magento_ImportExport_Model_Resource_Import_Data');
    }

    /**
     * Test getUniqueColumnData() in case when in data stored in requested column is unique
     */
    public function testGetUniqueColumnData()
    {
        $expectedBunches = Mage::registry('_fixture/Magento_ImportExport_Import_Data');

        $this->assertEquals($expectedBunches[0]['entity'], $this->_model->getUniqueColumnData('entity'));
    }

    /**
     * Test getUniqueColumnData() in case when in data stored in requested column is NOT unique
     *
     * @expectedException Magento_Core_Exception
     */
    public function testGetUniqueColumnDataException()
    {
        $this->_model->getUniqueColumnData('data');
    }

    /**
     * Test successful getBehavior()
     */
    public function testGetBehavior()
    {
        $expectedBunches = Mage::registry('_fixture/Magento_ImportExport_Import_Data');

        $this->assertEquals($expectedBunches[0]['behavior'], $this->_model->getBehavior());
    }

    /**
     * Test successful getEntityTypeCode()
     */
    public function testGetEntityTypeCode()
    {
        $expectedBunches = Mage::registry('_fixture/Magento_ImportExport_Import_Data');

        $this->assertEquals($expectedBunches[0]['entity'], $this->_model->getEntityTypeCode());
    }
}
