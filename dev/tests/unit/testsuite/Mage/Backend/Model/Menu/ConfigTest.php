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


class Mage_Backend_Model_Menu_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Modules_Reader
     */
    protected $_configMock;

    /**
     * @var Mage_Core_Model_CacheInterface
     */
    protected $_cacheInstanceMock;

    /**
     * @var DOMDocument
     */
    protected $_domDocumentMock;

    /**
     * @var Mage_Backend_Model_Menu_Director_Dom
     */
    protected $_directorDomMock;

    /**
     * @var Mage_Backend_Model_Menu_Config_Menu
     */
    protected $_configMenuMock;

    /**
     * @var Mage_Backend_Model_Menu_Builder
     */
    protected $_menuFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_itemFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuBuilderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logger;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var Mage_Backend_Model_Menu_Config
     */
    protected $_model;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config_Modules_Reader',
            array(), array(), '', false, false
        );

        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_objectManagerMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(array($this, 'getModelInstance')));
        $this->_objectManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this, 'get')));

        $this->_cacheInstanceMock = $this->getMock('Mage_Core_Model_Cache_Type_Config', array(), array(), '', false);

        $this->_directorDomMock = $this->getMock('Mage_Backend_Model_Menu_Director_Dom', array(), array(), '', false);

        $this->_menuFactoryMock = $this->getMock('Mage_Backend_Model_Menu_Factory', array(), array(), '', false);

        $this->_configMenuMock = $this->getMock('Mage_Backend_Model_Menu_Config_Menu', array(), array(), '', false);

        $this->_domDocumentMock = $this->getMock('DOMDocument', array(), array(), '', false);

        $this->_eventManagerMock = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false, false);

        $this->_logger = $this->getMock(
            'Mage_Core_Model_Logger', array('addStoreLog', 'log', 'logException'), array(), '', false
        );

        $this->_menuMock = $this->getMock('Mage_Backend_Model_Menu', array(), array(), '', false);

        $this->_menuBuilderMock = $this->getMock('Mage_Backend_Model_Menu_Builder', array(), array(), '', false);

        $this->_menuFactoryMock->expects($this->any())
            ->method('getMenuInstance')
            ->will($this->returnValue($this->_menuMock));        
        
        $this->_model = new Mage_Backend_Model_Menu_Config(
            $this->_cacheInstanceMock,
            $this->_objectManagerMock,
            $this->_configMock,
            $this->_eventManagerMock,
            $this->_logger,
            $this->_menuFactoryMock
        );
    }

    public function testGetMenuConfigurationFiles()
    {
        $this->_configMock->expects($this->any())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue(array(
                realpath(__DIR__) . '/../_files/menu_1.xml',
                realpath(__DIR__) . '/../_files/menu_2.xml'
            )
        ));
        $this->assertNotEmpty($this->_model->getMenuConfigurationFiles());
    }

    public function testGetMenuWithCachedObjectReturnsUnserializedObject()
    {
        $this->_cacheInstanceMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo(Mage_Backend_Model_Menu_Config::CACHE_MENU_OBJECT))
            ->will($this->returnValue('menu_cache'));

        $this->_menuMock->expects($this->once())
            ->method('unserialize')
            ->with('menu_cache');

        $this->assertEquals($this->_menuMock, $this->_model->getMenu());
    }

    public function testGetMenuWithNotCachedObjectBuidlsObject()
    {
        $this->_cacheInstanceMock->expects($this->at(0))
            ->method('load')
            ->with($this->equalTo(Mage_Backend_Model_Menu_Config::CACHE_MENU_OBJECT))
            ->will($this->returnValue(false));

        $this->_configMenuMock->expects($this->exactly(1))
            ->method('getMergedConfig')
            ->will($this->returnValue($this->_domDocumentMock));

        $this->_domDocumentMock->expects($this->exactly(1))
            ->method('saveXML')
            ->will($this->returnValue('<?xml version="1.0" encoding="utf-8"?><config><menu></menu></config>'));

        $this->_menuBuilderMock->expects($this->exactly(1))
            ->method('getResult')
            ->will($this->returnValue($this->_menuMock));

        $this->assertEquals($this->_menuMock, $this->_model->getMenu());
    }

    /**
     * @covers Mage_Backend_Model_Menu_Config::getMenu
     */
    public function testGetMenuWhenEnabledCache()
    {
        $xmlString = '<?xml version="1.0" encoding="utf-8"?><config><menu></menu></config>';

        $this->_cacheInstanceMock->expects($this->at(1))
            ->method('load')
            ->will($this->returnValue(false));

        $this->_cacheInstanceMock->expects($this->at(1))
            ->method('load')
            ->will($this->returnValue($xmlString));

        $this->_directorDomMock->expects($this->exactly(1))
            ->method('buildMenu')
            ->with($this->isInstanceOf('Mage_Backend_Model_Menu_Builder'));

        $this->_configMenuMock->expects($this->exactly(1))
            ->method('getMergedConfig')
            ->will($this->returnValue($this->_domDocumentMock));

        $this->_domDocumentMock->expects($this->exactly(1))
            ->method('saveXML')
            ->will($this->returnValue('<?xml version="1.0" encoding="utf-8"?><config><menu></menu></config>'));

        $this->_menuBuilderMock->expects($this->exactly(1))
            ->method('getResult')
            ->will($this->returnValue($this->_menuMock));

        $this->_model->getMenu();

        /*
         * Recall the same method to ensure that built menu cached in local protected property
         */
        $this->_model->getMenu();
    }

    /**
     * @covers Mage_Backend_Model_Menu_Config::getMenu
     */
    public function testGetMenuWhenCacheEnabledAndCleaned()
    {
        $xmlString = '<?xml version="1.0" encoding="utf-8"?><config><menu></menu></config>';

        $this->_configMock->expects($this->any())
            ->method('getModelInstance')
            ->will($this->returnCallback(array($this, 'getModelInstance')));

        $this->_cacheInstanceMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue(null));

        $this->_domDocumentMock->expects($this->exactly(1))
            ->method('saveXML')
            ->will($this->returnValue('<?xml version="1.0" encoding="utf-8"?><config><menu></menu></config>'));

        $this->_configMenuMock->expects($this->exactly(1))
            ->method('getMergedConfig')
            ->will($this->returnValue($this->_domDocumentMock));

        $this->_cacheInstanceMock->expects($this->at(2))
            ->method('save')
            ->with($this->equalTo($xmlString));

        $this->_cacheInstanceMock->expects($this->at(3))
            ->method('save')
            ->with($this->equalTo($this->_menuMock->serialize()));

        $this->_menuBuilderMock->expects($this->exactly(1))
            ->method('getResult')
            ->will($this->returnValue($this->_menuMock));

        $this->_model->getMenu();
    }

    public function testGetMenuTriggersEventOnlyOnceAfterMenuIsCreated()
    {
        $this->_eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('backend_menu_load_after'), $this->equalTo(array('menu' => $this->_menuMock)));

        $this->_configMenuMock->expects($this->once())
            ->method('getMergedConfig')
            ->will($this->returnValue($this->_domDocumentMock));

        $this->_menuBuilderMock->expects($this->exactly(1))
            ->method('getResult')
            ->will($this->returnValue($this->_menuMock));

        $this->_model->getMenu();
        $this->_model->getMenu();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetMenuInvalidArgumentExceptionLogged()
    {
        $this->_configMenuMock->expects($this->any())
            ->method('getMergedConfig')
            ->will($this->returnValue($this->_domDocumentMock));

        $this->_logger->expects($this->exactly(1))->method('logException')
            ->with($this->isInstanceOf('InvalidArgumentException'));

        $this->_menuBuilderMock->expects($this->exactly(1))
            ->method('getResult')
            ->will($this->throwException(new InvalidArgumentException()));

        $this->_model->getMenu();
    }

    public function testGetMenuGenericExceptionIsNotLogged()
    {
        $this->_configMenuMock->expects($this->any())
            ->method('getMergedConfig')
            ->will($this->returnValue($this->_domDocumentMock));

        $this->_logger->expects($this->never())->method('logException');

        $this->_menuBuilderMock->expects($this->exactly(1))
            ->method('getResult')
            ->will($this->throwException(new Exception()));
        try {
            $this->_model->getMenu();
        } catch (Exception $e) {
            return;
        }
        $this->fail("Generic Exception was not throwed");
    }

    /**
     * Callback method for mock object Mage_Core_Model_Config object
     *
     * @param mixed $model
     * @param mixed $arguments
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getModelInstance($model, $arguments)
    {
        if ($model == 'Mage_Backend_Model_Menu_Director_Dom') {
            return $this->_directorDomMock;
        } elseif ($model == 'Mage_Backend_Model_Menu_Config_Menu') {
            return $this->_configMenuMock;
        } elseif ($model == 'Mage_Backend_Model_Menu_Builder') {
            return $this->_menuBuilderMock;
        } elseif ($model == 'Mage_Core_Model_App') {
            $appMock = $this->getMock('Mage_Core_Model_App', array('getStore'), array(), '', false);
            $appMock->expects($this->any())
                ->method('getStore')
                ->will($this->returnValue($this->getMock('Mage_Core_Model_Store', array(), array(), '', false)));
            return $appMock;
        } else {
            return $this->getMock($model, array(), $arguments, '', false);
        }
    }

    /**
     * Callback method for mock object Mage_Core_Model_Config object
     *
     * @param mixed $model
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function get($model)
    {
        return $this->getModelInstance($model, array());
    }
}
