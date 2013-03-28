<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Cache_Frontend_PoolTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Cache_Frontend_Pool
     */
    protected $_model;

    /*
     * @var Mage_Core_Model_Config_Primary
     */
    protected $_configPrimary;

    public function setUp()
    {
        $this->_configPrimary = Mage::getSingleton('Mage_Core_Model_Config_Primary');
        $this->_model = new Mage_Core_Model_Cache_Frontend_Pool(
            $this->_configPrimary,
            Mage::getModel('Mage_Core_Model_Cache_Frontend_Factory')
        );
    }

    /**
     * @dataProvider cacheBackendsDataProvider
     */
    public function testDbCacheAdapter($cacheBackendName)
    {
        $cacheTypePath = Mage_Core_Model_Cache_Frontend_Pool::XML_PATH_SETTINGS_DEFAULT . '/backend';
        $oldCacheBackend = (string)$this->_configPrimary->getNode($cacheTypePath);
        $this->_configPrimary->setNode($cacheTypePath, $cacheBackendName);

        $cache = $this->_model->get(Mage_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID);
        $this->assertInstanceOf('Magento_Cache_FrontendInterface', $cache);
        $this->assertInstanceOf('Zend_Cache_Backend_Interface', $cache->getBackend());

        $this->_configPrimary->setNode($cacheTypePath, $oldCacheBackend);
    }

    public function cacheBackendsDataProvider()
    {
        return array(
            array('sqlite'),
            array('memcached'),
            array('apc'),
            array('xcache'),
            array('eaccelerator'),
            array('database'),
            array('File'),
            array('')
        );
    }
}
