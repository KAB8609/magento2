<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Menu_Item_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_Item_Factory
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $_helpers = array();

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_itemValidatorMock;

    /**
     * Constructor params
     *
     * @var array
     */
    protected $_params = array();

    public function setUp()
    {
        $this->_aclMock = $this->getMock('Mage_Core_Model_Authorization', array(), array(), '', false);
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_factoryMock = $this->getMock('Mage_Backend_Model_Menu_Factory');
        $this->_helpers = array(
            'Mage_Backend_Helper_Data' => $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false),
            'Mage_User_Helper_Data' => $this->getMock('Mage_User_Helper_Data')
        );
        $this->_urlModelMock = $this->getMock("Mage_Backend_Model_Url", array(), array(), '', false);
        $this->_appConfigMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_storeConfigMock = $this->getMock('Mage_Core_Model_Store_Config');
        $this->_itemValidatorMock = $this->getMock('Mage_Backend_Model_Menu_Item_Validator');

        $this->_params = array(
            'acl' => $this->_aclMock,
            'objectFactory' => $this->_objectFactoryMock,
            'menuFactory' => $this->_factoryMock,
            'helpers' => $this->_helpers,
            'urlModel' => $this->_urlModelMock,
            'appConfig' => $this->_appConfigMock,
            'storeConfig' => $this->_storeConfigMock,
            'validator' => $this->_itemValidatorMock
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider invalidArgumentsProvider
     */
    public function testConstructorWithWrongParameterTypesThrowsException($param)
    {
        $this->_params[$param] = new Varien_Object();
        new Mage_Backend_Model_Menu_Item_Factory($this->_params);
    }

    public function invalidArgumentsProvider()
    {
        return array(
            array('acl'),
            array('menuFactory'),
            array('urlModel'),
            array('appConfig'),
            array('storeConfig'),
            array('validator')
        );
    }

    public function testCreateFromArray()
    {
        $this->_objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with(
                $this->equalTo('Mage_Backend_Model_Menu_Item'),
                $this->equalTo(array(
                    'module' => $this->_helpers['Mage_User_Helper_Data'],
                    'dependsOnModule' => 'Mage_User_Helper_Data',
                    'title' => 'item1',
                    'acl' => $this->_aclMock,
                    'menuFactory' => $this->_factoryMock,
                    'urlModel' => $this->_urlModelMock,
                    'appConfig' => $this->_appConfigMock,
                    'storeConfig' => $this->_storeConfigMock,
                    'validator' => $this->_itemValidatorMock
                ))
        );
        $model = new Mage_Backend_Model_Menu_Item_Factory($this->_params);
        $model->createFromArray(array(
            'module' => 'Mage_User_Helper_Data',
            'title' => 'item1',
            'dependsOnModule' => 'Mage_User_Helper_Data'
        ));
    }

    public function testCreateFromArrayProvidesDefaultHelper()
    {
        $this->_objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with(
                $this->equalTo('Mage_Backend_Model_Menu_Item'),
                $this->equalTo(array(
                    'module' => $this->_helpers['Mage_Backend_Helper_Data'],
                    'acl' => $this->_aclMock,
                    'menuFactory' => $this->_factoryMock,
                    'urlModel' => $this->_urlModelMock,
                    'appConfig' => $this->_appConfigMock,
                    'storeConfig' => $this->_storeConfigMock,
                    'validator' => $this->_itemValidatorMock
                ))
        );
        $model = new Mage_Backend_Model_Menu_Item_Factory($this->_params);
        $model->createFromArray(array());
    }
}