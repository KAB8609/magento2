<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_PageCache_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\Observer
     */
    protected $_observer;

    protected function setUp()
    {
        $this->_observer = Mage::getModel('Magento\PageCache\Model\Observer');
    }

    /**
     * @magentoConfigFixture current_store system/external_page_cache/enabled 1
     */
    public function testSetNoCacheCookie()
    {
        /** @var $cookie \Magento\Core\Model\Cookie */
        $cookie = Mage::getSingleton('Magento\Core\Model\Cookie');
        $this->assertEmpty($cookie->get(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE));
        $this->_observer->setNoCacheCookie(new \Magento\Event\Observer());
        $this->assertNotEmpty($cookie->get(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE));
    }

    /**
     * @magentoConfigFixture current_store system/external_page_cache/enabled 1
     */
    public function testDeleteNoCacheCookie()
    {
        /** @var $cookie \Magento\Core\Model\Cookie */
        $cookie = Mage::getSingleton('Magento\Core\Model\Cookie');
        $cookie->set(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE, '1');
        $this->_observer->deleteNoCacheCookie(new \Magento\Event\Observer());
        $this->assertEmpty($cookie->get(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE));
    }
}
