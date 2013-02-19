<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_PageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_PageCache_Model_Observer
     */
    protected $_observer;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cookie;

    protected function setUp()
    {
        Mage::app()->getCacheInstance()->allowUse('full_page');
        $this->_cookie = $this->getMock(
            'Enterprise_PageCache_Model_Cookie',
            array('set', 'delete', 'updateCustomerCookies'),
            array(),
            '',
            false,
            false
        );

        $this->_observer = Mage::getObjectManager()
            ->create('Enterprise_PageCache_Model_Observer', array('cookie' => $this->_cookie));
    }

    protected function tearDown()
    {
        $this->_cookie = null;
        $this->_observer = null;
    }

    public function testProcessPreDispatchCanProcessRequest()
    {
        $request = new Magento_Test_Request();
        $response = new Magento_Test_Response();

        $request->setRouteName('catalog');
        $request->setControllerName('product');
        $request->setActionName('view');

        $observerData = new Varien_Event_Observer();
        $observerData->setEvent(new Varien_Event(array(
            'controller_action' => Mage::getModel(
                'Mage_Core_Controller_Front_Action',
                array('request' => $request, 'response' => $response)
            )
        )));

        $this->_cookie->expects($this->once())->method('updateCustomerCookies');

        Mage::app()->getCacheInstance()->allowUse(Mage_Core_Block_Abstract::CACHE_GROUP);

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('Mage_Catalog_Model_Session');
        $session->setParamsMemorizeDisabled(false);

        $this->_observer->processPreDispatch($observerData);

        $this->assertFalse(Mage::app()->useCache(Mage_Core_Block_Abstract::CACHE_GROUP));
        $this->assertTrue(Mage::getSingleton('Mage_Catalog_Model_Session')->getParamsMemorizeDisabled());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testProcessPreDispatchCannotProcessRequest()
    {
        /** @var $restriction Enterprise_PageCache_Model_Processor_Restriction */
        $restriction = Mage::getSingleton('Enterprise_PageCache_Model_Processor_Restriction');
        $restriction->setIsDenied();

        $observerData = new Varien_Event_Observer();
        $observerData->setEvent(new Varien_Event(array(
            'controller_action' => Mage::getModel(
                'Mage_Core_Controller_Front_Action',
                array('request' => new Magento_Test_Request(), 'response' => new Magento_Test_Response())
            )
        )));
        $this->_cookie
            ->expects($this->once())
            ->method('updateCustomerCookies')
        ;
        Mage::getSingleton('Mage_Catalog_Model_Session')->setParamsMemorizeDisabled(true);
        $this->_observer->processPreDispatch($observerData);
        $this->assertFalse(Mage::getSingleton('Mage_Catalog_Model_Session')->getParamsMemorizeDisabled());
    }

    public function testSetNoCacheCookie()
    {
        $this->_cookie
            ->expects($this->once())
            ->method('set')
            ->with(Enterprise_PageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
        ;
        $this->_observer->setNoCacheCookie(new Varien_Event_Observer());
    }

    public function testDeleteNoCacheCookie()
    {
        $this->_cookie
            ->expects($this->once())
            ->method('delete')
            ->with(Enterprise_PageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
        ;
        $this->_observer->deleteNoCacheCookie(new Varien_Event_Observer());
    }
}
