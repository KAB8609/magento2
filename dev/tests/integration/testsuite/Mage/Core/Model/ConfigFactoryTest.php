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
 * Second part of Mage_Core_Model_Config testing:
 * - Mage factory behaviour is tested
 *
 * @see Mage_Core_Model_ConfigTest
 */
class Mage_Core_Model_ConfigFactoryTest extends PHPUnit_Framework_TestCase
{
    protected static $_options = array();

    /** @var Mage_Core_Model_Config */
    protected $_model;

    public static function setUpBeforeClass()
    {
        self::$_options = Magento_Test_Bootstrap::getInstance()->getAppOptions();
    }

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Config(Mage::getObjectManager());
        $this->_model->init(self::$_options);
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @dataProvider classNameRewriteDataProvider
     */
    public function testClassNameRewrite($originalClass, $expectedClass, $classNameGetter)
    {
        $this->_model->setNode("global/rewrites/$originalClass", $expectedClass);
        $this->assertEquals($expectedClass, $this->_model->$classNameGetter($originalClass));
    }

    public function classNameRewriteDataProvider()
    {
        return array(
            'block'          => array('My_Module_Block_Class', 'Another_Module_Block_Class', 'getBlockClassName'),
            'helper'         => array('My_Module_Helper_Data', 'Another_Module_Helper_Data', 'getHelperClassName'),
            'model'          => array('My_Module_Model_Class', 'Another_Module_Model_Class', 'getModelClassName'),
            'resource model' => array(
                'My_Module_Model_Resource_Collection',
                'Another_Module_Model_Resource_Collection_New',
                'getResourceModelClassName'
            ),
        );
    }

    public function testGetBlockClassName()
    {
        $this->assertEquals('Mage_Core_Block_Template', $this->_model->getBlockClassName('Mage_Core_Block_Template'));
    }

    public function testGetHelperClassName()
    {
        $this->assertEquals('Mage_Core_Helper_Http', $this->_model->getHelperClassName('Mage_Core_Helper_Http'));
    }

    public function testGetResourceHelper()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $this->assertInstanceOf(
            'Mage_Core_Model_Resource_Helper_Abstract', $this->_model->getResourceHelper('Mage_Core')
        );
    }

    public function testGetModelClassName()
    {
        $this->assertEquals('Mage_Core_Model_Config', $this->_model->getModelClassName('Mage_Core_Model_Config'));
    }

    public function testGetModelInstance()
    {
        $this->assertInstanceOf('Mage_Core_Model_Config', $this->_model->getModelInstance('Mage_Core_Model_Config'));
    }

    public function testGetResourceModelClassName()
    {
        $this->assertEquals(
            'Mage_Core_Model_Resource_Config',
            $this->_model->getResourceModelClassName('Mage_Core_Model_Resource_Config')
        );
    }

    public function testGetResourceModelInstance()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $this->assertInstanceOf(
            'Mage_Core_Model_Resource_Config',
            $this->_model->getResourceModelInstance('Mage_Core_Model_Resource_Config')
        );
    }
}
