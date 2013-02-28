<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_PageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_PageCache_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_PageCache_Model_Observer
     */
    protected $_observer;

    protected function setUp()
    {
        $this->_observer = Mage::getModel('Mage_PageCache_Model_Observer');
    }

    /**
     * @magentoConfigFixture current_store system/external_page_cache/enabled 1
     */
    public function testSetNoCacheCookie()
    {
        /** @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('Mage_Core_Model_Cookie');
        $this->assertEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
        $this->_observer->setNoCacheCookie(new Varien_Event_Observer());
        $this->assertNotEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
    }

    /**
     * @magentoConfigFixture current_store system/external_page_cache/enabled 1
     */
    public function testDeleteNoCacheCookie()
    {
        /** @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('Mage_Core_Model_Cookie');
        $cookie->set(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE, '1');
        $this->_observer->deleteNoCacheCookie(new Varien_Event_Observer());
        $this->assertEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
    }
}
