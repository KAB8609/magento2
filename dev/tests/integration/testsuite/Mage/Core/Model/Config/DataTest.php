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

/**
 * @group module:Mage_Core
 */
class Mage_Core_Model_Config_DataTest extends PHPUnit_Framework_TestCase
{
    const SAMPLE_CONFIG_PATH = 'web/unsecure/base_url';
    const SAMPLE_VALUE = 'http://example.com/';

    /**
     * @var Mage_Core_Model_Config_Data
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        Mage::app()->getConfig()->saveConfig(self::SAMPLE_CONFIG_PATH, self::SAMPLE_VALUE);
        Magento_Test_Bootstrap::getInstance()->refreshConfiguration();
    }

    public static function tearDownAfterClass()
    {
        Mage::app()->getConfig()->deleteConfig(self::SAMPLE_CONFIG_PATH);
        Magento_Test_Bootstrap::getInstance()->refreshConfiguration();
    }

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Config_Data;
    }

    public function testIsValueChanged()
    {
        // load the model
        $collection = new Mage_Core_Model_Resource_Config_Data_Collection;
        $collection->addFieldToFilter('path', self::SAMPLE_CONFIG_PATH)->addFieldToFilter('scope_id', 0)
            ->addFieldToFilter('scope', 'default')
        ;
        foreach ($collection as $configData) {
            $this->_model = $configData;
            break;
        }
        $this->assertNotEmpty($this->_model->getId());

        // assert
        $this->assertFalse($this->_model->isValueChanged());
        $this->_model->setValue(uniqid());
        $this->assertTrue($this->_model->isValueChanged());
    }

    public function testGetOldValue()
    {
        $this->_model->setPath(self::SAMPLE_CONFIG_PATH);
        $this->assertEquals(self::SAMPLE_VALUE, $this->_model->getOldValue());

        $this->_model->setWebsiteCode('base');
        $this->assertEquals(self::SAMPLE_VALUE, $this->_model->getOldValue());

        $this->_model->setStoreCode('default');
        $this->assertEquals(self::SAMPLE_VALUE, $this->_model->getOldValue());
    }

    public function testGetFieldsetDataValue()
    {
        $this->assertNull($this->_model->getFieldsetDataValue('key'));
        $this->_model->setFieldsetData(array('key' => 'value'));
        $this->assertEquals('value', $this->_model->getFieldsetDataValue('key'));
    }

    public function testCRUD()
    {
        $this->_model->setData(
            array(
                'scope'     => 'default',
                'scope_id'  => 0,
                'path'      => 'test/config/path',
                'value'     => 'test value'
            )
        );
        $crud = new Magento_Test_Entity($this->_model, array('value' => 'new value'));
        $crud->testCrud();
    }

    public function testCollection()
    {
        $collection = $this->_model->getCollection();
        $collection->addScopeFilter('test', 0, 'test')
            ->addPathFilter('not_existing_path')
            ->addValueFilter('not_existing_value');
        $this->assertEmpty($collection->getItems());
    }
}
