<?php
/**
 * \Magento\Integration\Controller\Adminhtml
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Controller\Adminhtml;

use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockObjectManager;

    /** @var \Magento\Integration\Controller\Adminhtml\Integration */
    protected $_integrationContr;

    /** @var \Magento\TestFramework\Helper\ObjectManager $objectManagerHelper */
    protected $_objectManagerHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockApp;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockLayoutFilter;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockEventManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockTranslateModel;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockBackendModSess;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockBackendCntCtxt;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockIntegrationSvc;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockRegistry;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockRequest;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockResponse;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockConfigScope;

    /**
     * Setup object manager and initialize mocks
     */
    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager $objectManagerHelper */
        $this->_objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_setMageObjectManager();
        // Initialize mocks which are used in several test cases
        $this->_mockApp = $this->getMockBuilder('Magento\Core\Model\App')
            ->setMethods(array('getConfig'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockConfig = $this->getMockBuilder('Magento\Core\Model\Config')->disableOriginalConstructor()
            ->getMock();
        $this->_mockApp->expects($this->any())->method('getConfig')->will($this->returnValue($this->_mockConfig));
        $this->_mockEventManager = $this->getMockBuilder('Magento\Event\ManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockLayoutFilter = $this->getMockBuilder('Magento\Core\Model\Layout\Filter\Acl')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockBackendModSess = $this->getMockBuilder('Magento\Backend\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockTranslateModel = $this->getMockBuilder('Magento\Core\Model\Translate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockIntegrationSvc = $this->getMockBuilder('Magento\Integration\Service\IntegrationV1')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockRequest = $this->getMockBuilder('Magento\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockResponse = $this->getMockBuilder('Magento\App\Response\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockRegistry = $this->getMockBuilder('Magento\Core\Model\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockConfigScope = $this->getMockBuilder('Magento\Config\ScopeInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testIndexAction()
    {
        $this->_verifyLoadAndRenderLayout();
        // renderLayout
        $this->_integrationContr = $this->_createIntegrationController();
        $this->_integrationContr->indexAction();
    }

    public function testNewAction()
    {
        $this->_verifyLoadAndRenderLayout();
        // verify the request is forwarded to 'edit' action
        $this->_mockRequest->expects($this->any())->method('setActionName')->with('edit')
            ->will($this->returnValue($this->_mockRequest));
        $integrationContr = $this->_createIntegrationController();
        $integrationContr->newAction();
    }

    public function testEditAction()
    {
        $this->_mockIntegrationSvc->expects($this->any())->method('get')->with(1)->will(
            $this->returnValue($this->_getSampleIntegrationData()));
        $this->_mockRequest->expects($this->any())->method('getParam')->will($this->returnValue('1'));
        // put data in session, the magic function getFormData is called so, must match __call method name
        $this->_mockBackendModSess->expects($this->any())
            ->method('__call')->will(
                $this->returnValue(array(
                        Info::DATA_ID => 1,
                        'name' => 'testIntegration'
                    )));
        $this->_verifyLoadAndRenderLayout();
        $integrationContr = $this->_createIntegrationController();
        $integrationContr->editAction();
    }

    public function testEditActionNonExistentIntegration()
    {
        $exceptionMessage = 'This integration no longer exists.';
        // verify the error
        $this->_mockBackendModSess->expects($this->once())
            ->method('addError')
            ->with($this->equalTo($exceptionMessage));
        $this->_mockRequest->expects($this->any())->method('getParam')->will($this->returnValue('1'));
        // put data in session, the magic function getFormData is called so, must match __call method name
        $this->_mockBackendModSess->expects($this->any())
            ->method('__call')->will($this->returnValue(array('name' => 'nonExistentInt')));
        $this->_verifyLoadAndRenderLayout();
        $integrationContr = $this->_createIntegrationController();
        $integrationContr->editAction();
    }

    public function testEditActionNoDataAdd()
    {
        $exceptionMessage = 'Integration ID is not specified or is invalid.';
        // verify the error
        $this->_mockBackendModSess->expects($this->once())
            ->method('addError')
            ->with($this->equalTo($exceptionMessage));
        $this->_verifyLoadAndRenderLayout();
        $integrationContr = $this->_createIntegrationController();
        $integrationContr->editAction();
    }

    public function testEditException()
    {
        $exceptionMessage = 'Integration ID is not specified or is invalid.';
        // verify the error
        $this->_mockBackendModSess->expects($this->once())
            ->method('addError')
            ->with($this->equalTo($exceptionMessage));
        $this->_integrationContr = $this->_createIntegrationController();
        $this->_integrationContr->editAction();
    }

    public function testSaveAction()
    {
        // Use real translate model
        $this->_mockTranslateModel = null;
        $this->_mockRequest->expects($this->any())
            ->method('getPost')->will($this->returnValue(array(Integration::PARAM_INTEGRATION_ID => 1)));
        $this->_mockRequest->expects($this->any())->method('getParam')->will($this->returnValue('1'));
        $intData = $this->_getSampleIntegrationData();
        $this->_mockIntegrationSvc->expects($this->any())->method('get')->with(1)->will($this->returnValue($intData));
        $this->_mockIntegrationSvc->expects($this->any())->method('update')->with($this->anything())
            ->will($this->returnValue($intData));
        // verify success message
        $this->_mockBackendModSess->expects($this->once())->method('addSuccess')
            ->with(__('The integration \'%1\' has been saved.', $intData[Info::DATA_NAME]));
        $integrationContr = $this->_createIntegrationController();
        $integrationContr->saveAction();
    }

    public function testSaveActionException()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')->will($this->returnValue('1'));

        // Have integration service throw an exception to test exception path
        $exceptionMessage = 'an exception happened';
        $this->_mockIntegrationSvc->expects($this->any())
            ->method('get')
            ->with(1)
            ->will($this->throwException(new \Magento\Core\Exception($exceptionMessage)));
        // Verify error
        $this->_mockBackendModSess->expects($this->once())->method('addError')
            ->with($this->equalTo($exceptionMessage));
        $integrationContr = $this->_createIntegrationController();
        $integrationContr->saveAction();
    }

    public function testSaveActionNew()
    {
        $intData = $this->_getSampleIntegrationData();
        //No id when New Integration is Post-ed
        unset($intData[Info::DATA_ID]);
        $this->_mockRequest->expects($this->any())->method('getPost')->will($this->returnValue($intData));
        $intData[Info::DATA_ID] = 1;
        $this->_mockIntegrationSvc->expects($this->any())->method('create')->with($this->anything())
            ->will($this->returnValue($intData));
        $this->_mockIntegrationSvc->expects($this->any())->method('get')->with(1)->will(
            $this->returnValue(null));
        // Use real translate model
        $this->_mockTranslateModel = null;
        // verify success message
        $this->_mockBackendModSess->expects($this->once())->method('addSuccess')
            ->with(__('The integration \'%1\' has been saved.', $intData[Info::DATA_NAME]));
        $integrationContr = $this->_createIntegrationController();
        $integrationContr->saveAction();
    }

    /**
     * Makes sure that Mage has a mock object manager set.
     *
     */
    protected function _setMageObjectManager()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        \Magento\Core\Model\ObjectManager::setInstance($this->_mockObjectManager);
    }

    /**
     * Creates the IntegrationController to test.
     *
     * @return \Magento\Integration\Controller\Adminhtml\Integration
     */
    protected function _createIntegrationController()
    {
        // Mock Layout passed into constructor
        $layoutMock = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMergeMock = $this->getMockBuilder('Magento\Core\Model\Layout\Merge')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->any())->method('getUpdate')->will($this->returnValue($layoutMergeMock));
        $testElement = new \Magento\Simplexml\Element('<test>test</test>');
        $layoutMock->expects($this->any())->method('getNode')->will($this->returnValue($testElement));
        // for _setActiveMenu
        $blockMock = $this->getMockBuilder('Magento\Backend\Block\Menu')
            ->disableOriginalConstructor()
            ->getMock();
        $menuMock = $this->getMockBuilder('Magento\Backend\Model\Menu')
            ->disableOriginalConstructor()
            ->getMock();
        $loggerMock = $this->getMockBuilder('Magento\Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $loggerMock->expects($this->any())->method('logException')->will($this->returnSelf());
        $menuMock->expects($this->any())->method('getParentItems')->will($this->returnValue(array()));
        $blockMock->expects($this->any())->method('getMenuModel')->will($this->returnValue($menuMock));
        $layoutMock->expects($this->any())->method('getMessagesBlock')->will($this->returnValue($blockMock));
        $layoutMock->expects($this->any())->method('getBlock')->will($this->returnValue($blockMock));
        $contextParameters = array(
            'layout' => $layoutMock,
            'objectManager' => $this->_mockObjectManager,
            'session' => $this->_mockBackendModSess,
            'translator' => $this->_mockTranslateModel,
            'request' => $this->_mockRequest,
            'response' => $this->_mockResponse,
        );

        $this->_mockBackendCntCtxt = $this->_objectManagerHelper
            ->getObject(
                'Magento\Backend\Controller\Context',
                $contextParameters
            );
        $subControllerParams = array(
            'context' => $this->_mockBackendCntCtxt,
            'integrationService' => $this->_mockIntegrationSvc,
            'registry' => $this->_mockRegistry,
            'logger' => $loggerMock
        );
        /** Create IntegrationController to test */
        $integrationContr = $this->_objectManagerHelper
            ->getObject(
                'Magento\Integration\Controller\Adminhtml\Integration',
                $subControllerParams
            );
        return $integrationContr;
    }

    /**
     * Common mock 'expect' pattern.
     * Calls that need to be mocked out when
     * \Magento\Backend\Controller\AbstractAction loadLayout() and renderLayout() are called.
     */
    protected function _verifyLoadAndRenderLayout()
    {
        $map = array(
            array('Magento\Core\Model\Config', $this->_mockConfig),
            array('Magento\Core\Model\Layout\Filter\Acl', $this->_mockLayoutFilter),
            array('Magento\Backend\Model\Session', $this->_mockBackendModSess),
            array('Magento\Core\Model\Translate', $this->_mockTranslateModel),
            array('Magento\Config\ScopeInterface', $this->_mockConfigScope)
        );
        $this->_mockObjectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));
    }

    /**
     * Return sample Integration Data
     *
     * @return array
     */
    protected function _getSampleIntegrationData()
    {
        return array(
            Info::DATA_NAME => 'nameTest',
            Info::DATA_ID => '1',
            Info::DATA_EMAIL => 'test@magento.com',
            Info::DATA_ENDPOINT => 'http://magento.ll/endpoint'
        );
    }
}
