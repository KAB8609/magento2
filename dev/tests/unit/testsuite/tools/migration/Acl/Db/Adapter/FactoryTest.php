<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../../') . '/tools/migration/Acl/Db/Adapter/Factory.php';

class Tools_Migration_Acl_Db_Adapter_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tools_Migration_Acl_Db_Adapter_Factory
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_config;

    public function setUp()
    {
        $this->_model = new Tools_Migration_Acl_Db_Adapter_Factory();
        $this->_config = array(
            'dbname' => 'some_db_name',
            'password' => '',
            'username' => '',

        );
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @return array
     */
    public function getAdapterDataProvider()
    {
        return array(
            array('Varien_Db_Adapter_Pdo_Mysql'),
            array(''),
            array(null),
        );
    }

    /**
     * @param $adapterType
     * @dataProvider getAdapterDataProvider
     */
    public function testGetAdapter($adapterType)
    {
        $this->assertInstanceOf('Zend_Db_Adapter_Abstract',
            $this->_model->getAdapter($this->_config, $adapterType)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetAdapterWithInvalidType()
    {
        $this->_model->getAdapter($this->_config, 'Mage_Core_Helper_Data');
    }
}
