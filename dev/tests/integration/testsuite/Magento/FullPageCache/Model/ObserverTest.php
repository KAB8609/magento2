<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_FullPageCache_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_FullPageCache_Model_Observer
     */
    protected $_observer;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cookie;

    protected function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var Magento_Core_Model_Cache_StateInterface $cacheState */
        $cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get(
            'Magento_Core_Model_Cache_StateInterface');
        $cacheState->setEnabled('full_page', true);
        $this->_cookie = $this->getMock(
            'Magento_FullPageCache_Model_Cookie',
            array('set', 'delete', 'updateCustomerCookies'),
            array(),
            '',
            false,
            false
        );

        $this->_observer = $objectManager->create(
            'Magento_FullPageCache_Model_Observer', 
            array('cookie' => $this->_cookie)
        );
    }

    public function testProcessPreDispatchCanProcessRequest()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $request Magento_TestFramework_Request */
        $request = $objectManager->get('Magento_TestFramework_Request');
        $response = $objectManager->get('Magento_TestFramework_Response');

        $request->setRouteName('catalog');
        $request->setControllerName('product');
        $request->setActionName('view');

        $observerData = new Magento_Event_Observer();
        $arguments = array('request' => $request, 'response' => $response);
        $context = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Controller_Varien_Action_Context', $arguments);
        $observerData->setEvent(new Magento_Event(array(
            'controller_action' => Mage::getModel(
                'Magento_Core_Controller_Front_Action',
                array('context' => $context)
            )
        )));

        $this->_cookie->expects($this->once())->method('updateCustomerCookies');

        /** @var $cacheState Magento_Core_Model_Cache_StateInterface */
        $cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get(
            'Magento_Core_Model_Cache_StateInterface');

        $cacheState->setEnabled(Magento_Core_Block_Abstract::CACHE_GROUP, true);

        /** @var $session Magento_Catalog_Model_Session */
        $session = Mage::getSingleton('Magento_Catalog_Model_Session');
        $session->setParamsMemorizeDisabled(false);

        $this->_observer->processPreDispatch($observerData);

        $this->assertFalse($cacheState->isEnabled(Magento_Core_Block_Abstract::CACHE_GROUP));
        $this->assertTrue(Mage::getSingleton('Magento_Catalog_Model_Session')->getParamsMemorizeDisabled());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testProcessPreDispatchCannotProcessRequest()
    {
        /** @var $restriction Magento_FullPageCache_Model_Processor_RestrictionInterface */
        $restriction = Mage::getSingleton('Magento_FullPageCache_Model_Processor_RestrictionInterface');
        $restriction->setIsDenied();
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $request Magento_TestFramework_Request */
        $request = $objectManager->get('Magento_TestFramework_Request');
        $observerData = new Magento_Event_Observer();
        $arguments = array(
            'request' => $request,
            'response' => Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->get('Magento_TestFramework_Response')
        );
        $context = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Controller_Varien_Action_Context', $arguments);
        $observerData->setEvent(new Magento_Event(array(
            'controller_action' => Mage::getModel(
                'Magento_Core_Controller_Front_Action',
                array('context' => $context)
            )
        )));
        $this->_cookie
            ->expects($this->once())
            ->method('updateCustomerCookies');

        Mage::getSingleton('Magento_Catalog_Model_Session')->setParamsMemorizeDisabled(true);
        $this->_observer->processPreDispatch($observerData);
        $this->assertFalse(Mage::getSingleton('Magento_Catalog_Model_Session')->getParamsMemorizeDisabled());
    }

    public function testSetNoCacheCookie()
    {
        $this->_cookie
            ->expects($this->once())
            ->method('set')
            ->with(Magento_FullPageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
        ;
        $this->_observer->setNoCacheCookie(new Magento_Event_Observer());
    }

    public function testDeleteNoCacheCookie()
    {
        $this->_cookie
            ->expects($this->once())
            ->method('delete')
            ->with(Magento_FullPageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
        ;
        $this->_observer->deleteNoCacheCookie(new Magento_Event_Observer());
    }
}
