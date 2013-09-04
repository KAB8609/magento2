<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_StoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Store|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $params = array(
            'context'         => $objectManager->get('Magento_Core_Model_Context'),
            'configCacheType' => $objectManager->get('Magento_Core_Model_Cache_Type_Config'),
            'urlModel'        => $objectManager->get('Magento_Core_Model_Url'),
            'appState'        => $objectManager->get('Magento_Core_Model_App_State'),
            'coreStoreConfig' => $objectManager->get('Magento_Core_Model_Store_Config')
        );

        $this->_model = $this->getMock(
            'Magento_Core_Model_Store',
            array('getUrl'),
            $params
        );
    }

    /**
     * @dataProvider loadDataProvider
     */
    public function testLoad($loadId, $expectedId)
    {
        $this->_model->load($loadId);
        $this->assertEquals($expectedId, $this->_model->getId());
    }

    public function loadDataProvider()
    {
        return array(
            array(1, 1),
            array('default', 1),
            array('nostore',null),
        );
    }

    public function testSetGetConfig()
    {
        /* config operations require store to be loaded */
        $this->_model->load('default');
        $value = $this->_model->getConfig(Magento_Core_Model_Store::XML_PATH_STORE_IN_URL);
        $this->_model->setConfig(Magento_Core_Model_Store::XML_PATH_STORE_IN_URL, 'test');
        $this->assertEquals('test', $this->_model->getConfig(Magento_Core_Model_Store::XML_PATH_STORE_IN_URL));
        $this->_model->setConfig(Magento_Core_Model_Store::XML_PATH_STORE_IN_URL, $value);

        /* Call set before get */
        $this->_model->setConfig(Magento_Core_Model_Store::XML_PATH_USE_REWRITES, 1);
        $this->assertEquals(1, $this->_model->getConfig(Magento_Core_Model_Store::XML_PATH_USE_REWRITES));
        $this->_model->setConfig(Magento_Core_Model_Store::XML_PATH_USE_REWRITES, 0);
    }

    public function testSetGetWebsite()
    {
        $this->assertFalse($this->_model->getWebsite());
        $website = Mage::app()->getWebsite();
        $this->_model->setWebsite($website);
        $actualResult = $this->_model->getWebsite();
        $this->assertSame($website, $actualResult);
    }

    public function testSetGetGroup()
    {
        $this->assertFalse($this->_model->getGroup());
        $storeGroup = Mage::app()->getGroup();
        $this->_model->setGroup($storeGroup);
        $actualResult = $this->_model->getGroup();
        $this->assertSame($storeGroup, $actualResult);
    }

    /**
     * Isolation is enabled, as we pollute config with rewrite values
     *
     * @param string $type
     * @param bool $useRewrites
     * @param bool $useStoreCode
     * @param string $expected
     * @dataProvider getBaseUrlDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetBaseUrl($type, $useRewrites, $useStoreCode, $expected)
    {
        /* config operations require store to be loaded */
        $this->_model->load('default');
        $this->_model->setConfig(Magento_Core_Model_Store::XML_PATH_USE_REWRITES, $useRewrites);
        $this->_model->setConfig(Magento_Core_Model_Store::XML_PATH_STORE_IN_URL, $useStoreCode);

        $actual = $this->_model->getBaseUrl($type);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function getBaseUrlDataProvider()
    {
        return array(
            array(Magento_Core_Model_Store::URL_TYPE_WEB, false, false, 'http://localhost/'),
            array(Magento_Core_Model_Store::URL_TYPE_WEB, false, true,  'http://localhost/'),
            array(Magento_Core_Model_Store::URL_TYPE_WEB, true,  false, 'http://localhost/'),
            array(Magento_Core_Model_Store::URL_TYPE_WEB, true,  true,  'http://localhost/'),
            array(Magento_Core_Model_Store::URL_TYPE_LINK, false, false, 'http://localhost/index.php/'),
            array(Magento_Core_Model_Store::URL_TYPE_LINK, false, true,  'http://localhost/index.php/default/'),
            array(Magento_Core_Model_Store::URL_TYPE_LINK, true,  false, 'http://localhost/'),
            array(Magento_Core_Model_Store::URL_TYPE_LINK, true,  true,  'http://localhost/default/'),
            array(Magento_Core_Model_Store::URL_TYPE_DIRECT_LINK, false, false, 'http://localhost/index.php/'),
            array(Magento_Core_Model_Store::URL_TYPE_DIRECT_LINK, false, true,  'http://localhost/index.php/'),
            array(Magento_Core_Model_Store::URL_TYPE_DIRECT_LINK, true,  false, 'http://localhost/'),
            array(Magento_Core_Model_Store::URL_TYPE_DIRECT_LINK, true,  true,  'http://localhost/'),
            array(Magento_Core_Model_Store::URL_TYPE_STATIC, false, false, 'http://localhost/pub/static/'),
            array(Magento_Core_Model_Store::URL_TYPE_STATIC, false, true,  'http://localhost/pub/static/'),
            array(Magento_Core_Model_Store::URL_TYPE_STATIC, true,  false, 'http://localhost/pub/static/'),
            array(Magento_Core_Model_Store::URL_TYPE_STATIC, true,  true,  'http://localhost/pub/static/'),
            array(Magento_Core_Model_Store::URL_TYPE_CACHE, false, false, 'http://localhost/pub/cache/'),
            array(Magento_Core_Model_Store::URL_TYPE_CACHE, false, true,  'http://localhost/pub/cache/'),
            array(Magento_Core_Model_Store::URL_TYPE_CACHE, true,  false, 'http://localhost/pub/cache/'),
            array(Magento_Core_Model_Store::URL_TYPE_CACHE, true,  true,  'http://localhost/pub/cache/'),
            array(Magento_Core_Model_Store::URL_TYPE_LIB, false, false, 'http://localhost/pub/lib/'),
            array(Magento_Core_Model_Store::URL_TYPE_LIB, false, true,  'http://localhost/pub/lib/'),
            array(Magento_Core_Model_Store::URL_TYPE_LIB, true,  false, 'http://localhost/pub/lib/'),
            array(Magento_Core_Model_Store::URL_TYPE_LIB, true,  true,  'http://localhost/pub/lib/'),
            array(Magento_Core_Model_Store::URL_TYPE_MEDIA, false, false, 'http://localhost/pub/media/'),
            array(Magento_Core_Model_Store::URL_TYPE_MEDIA, false, true,  'http://localhost/pub/media/'),
            array(Magento_Core_Model_Store::URL_TYPE_MEDIA, true,  false, 'http://localhost/pub/media/'),
            array(Magento_Core_Model_Store::URL_TYPE_MEDIA, true,  true,  'http://localhost/pub/media/'),
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetBaseUrlInPub()
    {
        Magento_Test_Helper_Bootstrap::getInstance()->reinitialize(array(
            Mage::PARAM_APP_URIS => array(Magento_Core_Model_Dir::PUB => '')
        ));
        $this->_model->load('default');

        $this->assertEquals(
            'http://localhost/static/',
            $this->_model->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_STATIC)
        );
        $this->assertEquals(
            'http://localhost/lib/',
            $this->_model->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_LIB)
        );
        $this->assertEquals(
            'http://localhost/media/',
            $this->_model->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_MEDIA)
        );
    }

    /**
     * Isolation is enabled, as we pollute config with rewrite values
     *
     * @param string $type
     * @param bool $useCustomEntryPoint
     * @param bool $useStoreCode
     * @param string $expected
     * @dataProvider getBaseUrlForCustomEntryPointDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetBaseUrlForCustomEntryPoint($type, $useCustomEntryPoint, $useStoreCode, $expected)
    {
        /* config operations require store to be loaded */
        $this->_model->load('default');
        $this->_model->setConfig(Magento_Core_Model_Store::XML_PATH_USE_REWRITES, false);
        $this->_model->setConfig(Magento_Core_Model_Store::XML_PATH_STORE_IN_URL, $useStoreCode);

        // emulate custom entry point
        $_SERVER['SCRIPT_FILENAME'] = 'custom_entry.php';
        if ($useCustomEntryPoint) {
            Mage::register('custom_entry_point', true);
        }
        $actual = $this->_model->getBaseUrl($type);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function getBaseUrlForCustomEntryPointDataProvider()
    {
        return array(
            array(Magento_Core_Model_Store::URL_TYPE_LINK, false, false, 'http://localhost/custom_entry.php/'),
            array(Magento_Core_Model_Store::URL_TYPE_LINK, false, true,  'http://localhost/custom_entry.php/default/'),
            array(Magento_Core_Model_Store::URL_TYPE_LINK, true, false, 'http://localhost/index.php/'),
            array(Magento_Core_Model_Store::URL_TYPE_LINK, true, true,  'http://localhost/index.php/default/'),
            array(Magento_Core_Model_Store::URL_TYPE_DIRECT_LINK, false, false, 'http://localhost/custom_entry.php/'),
            array(Magento_Core_Model_Store::URL_TYPE_DIRECT_LINK, false, true,  'http://localhost/custom_entry.php/'),
            array(Magento_Core_Model_Store::URL_TYPE_DIRECT_LINK, true,  false, 'http://localhost/index.php/'),
            array(Magento_Core_Model_Store::URL_TYPE_DIRECT_LINK, true,  true,  'http://localhost/index.php/'),
        );
    }

    public function testGetDefaultCurrency()
    {
        /* currency operations require store to be loaded */
        $this->_model->load('default');
        $this->assertEquals($this->_model->getDefaultCurrencyCode(), $this->_model->getDefaultCurrency()->getCode());
    }

    /**
     * @todo refactor Magento_Core_Model_Store::getPriceFilter, it can return two different types
     */
    public function testGetPriceFilter()
    {
        $this->assertInstanceOf('Magento_Directory_Model_Currency_Filter', $this->_model->getPriceFilter());
    }

    public function testIsCanDelete()
    {
        $this->assertFalse($this->_model->isCanDelete());
        $this->_model->load(1);
        $this->assertFalse($this->_model->isCanDelete());
        $this->_model->setId(100);
        $this->assertTrue($this->_model->isCanDelete());
    }

    public function testGetCurrentUrl()
    {
        $this->_model->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('http://localhost/index.php'));
        $this->assertStringEndsWith('default', $this->_model->getCurrentUrl());
        $this->assertStringEndsNotWith('default', $this->_model->getCurrentUrl(false));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        $this->_model->setData(
            array(
                'code'          => 'test',
                'website_id'    => 1,
                'group_id'      => 1,
                'name'          => 'test name',
                'sort_order'    => 0,
                'is_active'     => 1
            )
        );

        /* emulate admin store */
        Mage::app()->getStore()->setId(Magento_Core_Model_AppInterface::ADMIN_STORE_ID);
        $crud = new Magento_Test_Entity($this->_model, array('name' => 'new name'));
        $crud->testCrud();
    }

    /**
     * @param array $badStoreData
     *
     * @dataProvider saveValidationDataProvider
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @expectedException Magento_Core_Exception
     */
    public function testSaveValidation($badStoreData)
    {
        $normalStoreData = array(
            'code'          => 'test',
            'website_id'    => 1,
            'group_id'      => 1,
            'name'          => 'test name',
            'sort_order'    => 0,
            'is_active'     => 1
        );
        $data = array_merge($normalStoreData, $badStoreData);

        $this->_model->setData($data);

        /* emulate admin store */
        Mage::app()->getStore()->setId(Magento_Core_Model_App::ADMIN_STORE_ID);
        $this->_model->save();
    }

    /**
     * @return array
     */
    public static function saveValidationDataProvider()
    {
        return array(
            'empty store name' => array(
                array('name' => '')
            ),
            'empty store code' => array(
                array('code' => '')
            ),
            'invalid store code' => array(
                array('code' => '^_^')
            ),
        );
    }

    /**
     * @dataProvider isUseStoreInUrlDataProvider
     */
    public function testIsUseStoreInUrl($isInstalled, $storeInUrl, $storeId, $expectedResult)
    {
        $appStateMock = $this->getMock('Magento_Core_Model_App_State', array(), array(), '', false, false);
        $appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue($isInstalled));

        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $params = array(
            'context'         => $objectManager->get('Magento_Core_Model_Context'),
            'configCacheType' => $objectManager->get('Magento_Core_Model_Cache_Type_Config'),
            'urlModel'        => $objectManager->get('Magento_Core_Model_Url'),
            'appState'        => $appStateMock,
            'coreStoreConfig' => $objectManager->get('Magento_Core_Model_Store_Config')
        );

        $model = $this->getMock('Magento_Core_Model_Store', array('getConfig'), $params);


        $model->expects($this->any())->method('getConfig')
            ->with($this->stringContains(Magento_Core_Model_Store::XML_PATH_STORE_IN_URL))
            ->will($this->returnValue($storeInUrl));
        $model->setStoreId($storeId);
        $this->assertEquals($model->isUseStoreInUrl(), $expectedResult);
    }

    /**
     * @see self::testIsUseStoreInUrl;
     * @return array
     */
    public function isUseStoreInUrlDataProvider()
    {
        return array(
            array(true, true, 1, true),
            array(false, true, 1, false),
            array(true, false, 1, false),
            array(true, true, 0, false),
        );
    }
}
