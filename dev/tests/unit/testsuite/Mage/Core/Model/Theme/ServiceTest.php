<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme service model
 */
class Mage_Core_Model_Theme_ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configCacheTypeMock;

    /**
     * @var Mage_Core_Model_Theme_Service
     */
    protected $_model;
    
    protected function setUp()
    {
        /** @var $this->_themeMock Mage_Core_Model_Theme */
        $this->_themeMock = $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false);
        $this->_themeFactoryMock = $this->getMock('Mage_Core_Model_Theme_Factory', array('create'), array(), '', false);
        $this->_themeFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_themeMock));
        $this->_configCacheTypeMock = $this->getMock('Mage_Core_Model_Cache_Type_Config', array(), array(), '', false);
        $this->_model = new Mage_Core_Model_Theme_Service($this->_themeFactoryMock,
            $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_App', array(), array(), '', false),
            $this->getMock('Mage_Core_Helper_Data', array(), array(), '', false),
            $this->getMock('Mage_DesignEditor_Model_Resource_Layout_Update', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Config_Storage_WriterInterface', array(), array(), '', false),
            $this->_configCacheTypeMock
        );
    }

    protected function tearDown()
    {
        $this->_themeMock = null;
        $this->_themeFactoryMock = null;
        $this->_configCacheTypeMock = null;
        $this->_model = null;
    }
    
    /**
     * @dataProvider isCustomizationsExistDataProvider
     * @param array $availableIsVirtual
     * @param bool $expectedResult
     */
    public function testIsCustomizationsExist($availableIsVirtual, $expectedResult)
    {
        $themeCollectionMock = array();
        foreach ($availableIsVirtual as $isVirtual) {
            /** @var $themeItemMock Mage_Core_Model_Theme */
            $themeItemMock = $this->getMock('Mage_Core_Model_Theme', array('isVirtual'), array(), '', false);
            $themeItemMock->expects($this->any())
                ->method('isVirtual')
                ->will($this->returnValue($isVirtual));
            $themeCollectionMock[] = $themeItemMock;
        }

        $this->_themeMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($themeCollectionMock));

        $this->assertEquals($expectedResult, $this->_model->isCustomizationsExist());
    }

    /**
     * @return array
     */
    public function isCustomizationsExistDataProvider()
    {
        return array(
            array(array(false, false, false), false),
            array(array(false, true, false), true)
        );
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Theme is not recognized. Requested id: -1
     */
    public function testAssignThemeToStoresWrongThemeId()
    {
        $this->_themeMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo(-1))
            ->will($this->returnValue($this->_themeMock));
        $this->_themeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(false));

        $this->_model->assignThemeToStores(-1);
    }

    /**
     * @covers Mage_Core_Model_Theme_Service::getAssignedThemeCustomizations
     * @covers Mage_Core_Model_Theme_Service::getUnassignedThemeCustomizations
     * @dataProvider getAssignedUnassignedThemesDataProvider
     * @param array $stores
     * @param array $themes
     * @param array $expAssignedThemes
     * @param array $expUnassignedThemes
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testGetAssignedAndUnassignedThemes($stores, $themes, $expAssignedThemes, $expUnassignedThemes)
    {
        $index = 0;
        /* Mock assigned themeId to each store */
        $storeConfigMock = $this->getMock('Mage_Core_Model_Store', array('getId'), array(), '', false);
        $storeMockCollection = array();
        foreach ($stores as $thisId) {
            $storeConfigMock->expects($this->at($index++))
                ->method('getId')
                ->will($this->returnValue($thisId));

            $storeMockCollection[] = $storeConfigMock;
        }

        /* Mock list existing stores */
        $appMock = $this->getMock('Mage_Core_Model_App', array('getStores'), array(), '', false);
        $appMock->expects($this->once())
            ->method('getStores')
            ->will($this->returnValue($storeMockCollection));

        /* Mock list customized themes */
        $themesMock = array();
        foreach ($themes as $themeId) {
            /** @var $theme Mage_Core_Model_Theme */
            $theme = $this->getMock('Mage_Core_Model_Theme', array('getId'), array(), '', false);
            $theme->expects($this->any())->method('getId')->will($this->returnValue($themeId));
            $themesMock[] = $theme;
        }

        $designMock = $this->getMock('Mage_Core_Model_Design_Package', array('getConfigurationDesignTheme'),
            array(), '', false);
        $designMock->expects($this->any())
            ->method('getConfigurationDesignTheme')
            ->with($this->anything(), $this->arrayHasKey('store'))
            ->will($this->returnCallback(
                function ($area, $params) {
                    return $params['store']->getId();
                }
            ));
        $helperMock = $this->getMock('Mage_Core_Helper_Data', array(), array(), '', false);
        $layoutUpdateMock = $this->getMock('Mage_DesignEditor_Model_Resource_Layout_Update', array(), array(), '',
            false
        );
        $eventManagerMock = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '',
            false
        );

        $themeFactoryMock = $this->getMock('Mage_Core_Model_Theme_Factory', array('create'), array(), '', false);
        $themeFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->getMock('Mage_Core_Model_Theme', array(), array(), '', false)));

        $writerMock = $this->getMock('Mage_Core_Model_Config_Storage_WriterInterface', array(), array(), '', false);
        $configCacheTypeMock = $this->getMock('Mage_Core_Model_Cache_Type_Config', array(), array(), '', false);
        /** @var $themeService Mage_Core_Model_Theme_Service */
        $themeService = $this->getMock('Mage_Core_Model_Theme_Service', array('_getThemeCustomizations'),
            array($themeFactoryMock, $designMock, $appMock, $helperMock,
                $layoutUpdateMock, $eventManagerMock, $writerMock, $configCacheTypeMock)
        );
        $themeService->expects($this->once())
            ->method('_getThemeCustomizations')
            ->will($this->returnValue($themesMock));

        $assignedThemes = $themeService->getAssignedThemeCustomizations();
        $unassignedThemes = $themeService->getUnassignedThemeCustomizations();

        $assignedData = array();
        foreach ($assignedThemes as $theme) {
            $assignedData[$theme->getId()] = $theme->getAssignedStores();
        }
        $this->assertEquals(array_keys($expAssignedThemes), array_keys($assignedData));


        $unassignedData = array();
        foreach ($unassignedThemes as $theme) {
            $unassignedData[] = $theme->getId();
        }
        $this->assertEquals($expUnassignedThemes, $unassignedData);
    }

    /**
     * @return array()
     */
    public function getAssignedUnassignedThemesDataProvider()
    {
        return array(
            array(
                'stores' => array(
                    'store_1' => 1,
                    'store_2' => 4,
                    'store_3' => 3,
                    'store_4' => 8,
                    'store_5' => 3,
                    'store_6' => 10,
                ),
                'themes' => array(1, 2, 3, 4, 5, 6, 7, 8, 9),
                'expectedAssignedThemes' => array(
                    1 => array('store_1'),
                    3 => array('store_3', 'store_5'),
                    4 => array('store_2'),
                    8 => array('store_4'),
                ),
                'expectedUnassignedThemes' => array(2, 5, 6, 7, 9)
            )
        );
    }
}
