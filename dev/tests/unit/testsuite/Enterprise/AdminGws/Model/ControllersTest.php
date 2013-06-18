<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_AdminGws
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_AdminGws_Model_ControllersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_AdminGws_Model_Controllers
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_roleMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * Controller request object
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ctrlRequestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_controllerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactory;

    public function setUp()
    {
        $this->_roleMock = $this->getMock('Enterprise_AdminGws_Model_Role', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_objectFactory = $this->getMock('Magento_ObjectManager', array(), array(), '', false);

        $this->_controllerMock = $this->getMock('Mage_Adminhtml_Controller_Action', array(), array(), '', false);
        $this->_ctrlRequestMock = $this->getMock(
            'Mage_Core_Controller_Request_Http',
            array(),
            array(),
            '',
            false
        );
        $this->_controllerMock->expects($this->once())
            ->method('getRequest')->will($this->returnValue($this->_ctrlRequestMock));

        $this->_model = new Enterprise_AdminGws_Model_Controllers(
            $this->_roleMock,
            $this->_requestMock,
            $this->_objectFactory
        );
    }

    public function tearDown()
    {
        unset($this->_controllerMock);
        unset($this->_ctrlRequestMock);
        unset($this->_model);
        unset($this->_objectFactory);
        unset($this->_requestMock);
        unset($this->_roleMock);
    }

    /**
     * Test deny access if role has no allowed website ids and there are considering actions to deny
     */
    public function testValidateRuleEntityActionRoleHasntWebSiteIdsAndConsideringActionsToDenyForwardAvoidCycling()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));

        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('denied'));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(null));

        $this->_model->validateRuleEntityAction($this->_controllerMock);
    }

    /**
     * Test deny access if role has no allowed website ids and there are considering actions to deny
     */
    public function testValidateRuleEntityActionRoleHasntWebSiteIdsAndConsideringActionsToDenyForward()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));

        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('any_action'));
        $this->_requestMock->expects($this->once())->method('initForward');
        $this->_requestMock->expects($this->once())->method('setActionName')
            ->with($this->equalTo('denied'))->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setDispatched')->with($this->equalTo(false));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(null));

        $this->_model->validateRuleEntityAction($this->_controllerMock);
    }

    /**
     * Test stop further validating if role has any allowed website ids and
     * there are considering any action which is not in deny list
     */
    public function testValidateRuleEntityActionWhichIsNotInDenyList()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('any_action'));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(null));
        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /**
     * Test stop further validating if there is no an appropriate entity id in request params
     */
    public function testValidateRuleEntityActionNoAppropriateEntityIdInRequestParams()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(null));
        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));
        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /**
     * Test get valid entity model class name
     * @param $controllerName string
     * @param $modelName string
     * @dataProvider validateRuleEntityActionGetValidModuleClassNameDataProvider
     */
    public function testValidateRuleEntityActionGetValidModuleClassName($controllerName, $modelName)
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getControllerName')->will($this->returnValue($controllerName));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(1));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));

        $this->_objectFactory->expects($this->once())
            ->method('create')->with($this->equalTo($modelName))->will($this->returnValue(null));

        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    public function validateRuleEntityActionGetValidModuleClassNameDataProvider()
    {
        return array(
            array(
                'promo_catalog',
                'Mage_CatalogRule_Model_Rule',
            ),
            array(
                'promo_quote',
                'Mage_SalesRule_Model_Rule'
            ),
            array(
                'reminder',
                'Enterprise_Reminder_Model_Rule'
            ),
            array(
                'customersegment',
                'Enterprise_CustomerSegment_Model_Segment'
            ),
        );
    }

    /*
     * Test get entity model class name invalid controller name
     */
    public function testValidateRuleEntityActionGetModuleClassNameWithInvalidController()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects($this->once())
            ->method('getControllerName')->will($this->returnValue('some_other'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(1));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));

        $this->_objectFactory->expects($this->exactly(0))->method('create');

        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /*
     * Test deny action if specified rule entity doesn't exist
     */
    public function testValidateRuleEntityActionDenyActionIfSpecifiedRuleEntityDoesntExist()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getControllerName')->will($this->returnValue('promo_catalog'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(1));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));

        $modelMock = $this->getMock('Mage_CatalogRule_Model_Rule', array(), array(), '', false);
        $modelMock->expects($this->once())->method('load')->with(1);
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(false));

        $this->_objectFactory->expects($this->exactly(1))
            ->method('create')->will($this->returnValue($modelMock));

        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('denied'));

        $this->assertEmpty($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /*
     * Test deny actions what lead to changing data if role has no exclusive access to assigned to rule entity websites
     */
    public function testValidateRuleEntityActionDenyActionIfRoleHasNoExclusiveAccessToAssignedToRuleEntityWebsites()
    {
        $modelMock = $this->getMock('Mage_CatalogRule_Model_Rule', array(), array(), '', false);

        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock->expects($this->once())
            ->method('getControllerName')->will($this->returnValue('promo_catalog'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(array(1)));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));
        $this->_roleMock->expects($this->once())
            ->method('hasExclusiveAccess')->with($this->equalTo(array(0 => 1, 2 => 2)))
            ->will($this->returnValue(false));

        $this->_objectFactory->expects($this->exactly(1))
            ->method('create')->will($this->returnValue($modelMock));

        $modelMock->expects($this->once())->method('load')->with(array(1));
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $modelMock->expects($this->once())->method('getOrigData')->will($this->returnValue(array(1, 2)));

        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('denied'));

        $this->assertEmpty($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /*
     * Test deny action if role has no access to assigned to rule entity websites
     */
    public function testValidateRuleEntityActionDenyActionIfRoleHasNoAccessToAssignedToRuleEntityWebsites()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock
            ->expects($this->any())->method('getParam')->will($this->returnValue(array(1)));
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getControllerName')->will($this->returnValue('promo_catalog'));

        $modelMock = $this->getMock('Mage_CatalogRule_Model_Rule', array(), array(), '', false);
        $modelMock->expects($this->once())->method('load')->with(array(1));
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $modelMock->expects($this->once())->method('getOrigData')->will($this->returnValue(array(1, 2)));

        $this->_objectFactory->expects($this->exactly(1))
            ->method('create')->will($this->returnValue($modelMock));
        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));

        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('denied'));

        $this->_roleMock->expects($this->once())
            ->method('hasExclusiveAccess')->with($this->equalTo(array(0 => 1, 2 => 2)))
            ->will($this->returnValue(true));

        $this->_roleMock->expects($this->once())
            ->method('hasWebsiteAccess')->with($this->equalTo(array(0 => 1, 2 => 2)))
            ->will($this->returnValue(false));

        $this->assertEmpty($this->_model->validateRuleEntityAction($this->_controllerMock));
    }

    /*
     * Test validate rule entity action with valid params
     */
    public function testValidateRuleEntityActionWithValidParams()
    {
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getActionName')->will($this->returnValue('edit'));
        $this->_ctrlRequestMock
            ->expects($this->once())->method('getControllerName')->will($this->returnValue('promo_catalog'));
        $this->_ctrlRequestMock->expects($this->any())->method('getParam')->will($this->returnValue(array(1)));

        $this->_roleMock->expects($this->once())->method('getWebsiteIds')->will($this->returnValue(array(1)));

        $modelMock = $this->getMock('Mage_CatalogRule_Model_Rule', array(), array(), '', false);
        $modelMock->expects($this->once())->method('load')->with(array(1));
        $modelMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $modelMock->expects($this->once())->method('getOrigData')->will($this->returnValue(array(1, 2)));

        $this->_objectFactory->expects($this->exactly(1))
            ->method('create')->will($this->returnValue($modelMock));

        $this->_roleMock->expects($this->once())
            ->method('hasExclusiveAccess')->with($this->equalTo(array(0 => 1, 2 => 2)))
            ->will($this->returnValue(true));

        $this->_roleMock->expects($this->once())
            ->method('hasWebsiteAccess')->with($this->equalTo(array(0 => 1, 2 => 2)))
            ->will($this->returnValue(true));

        $this->assertTrue($this->_model->validateRuleEntityAction($this->_controllerMock));
    }
}
