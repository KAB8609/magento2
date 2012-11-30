<?php
/**
 * Test for Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Resource_Acl_Rule|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleResource;

    /**
     * @var Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource
     */
    protected $_block;

    protected function setUp()
    {
        $this->_ruleResource = $this->getMockBuilder('Mage_Webapi_Model_Resource_Acl_Rule')
            ->disableOriginalConstructor()
            ->setMethods(array('getResourceIdsByRole'))
            ->getMock();

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $helper->getBlock('Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource', array(
            // TODO Remove injecting of 'urlBuilder' and 'authorizationConfig' after MAGETWO-5038 complete
            'urlBuilder' => $this->getMockBuilder('Mage_Backend_Model_Url')
                ->disableOriginalConstructor()
                ->getMock(),
            'authorizationConfig' => $this->getMockBuilder('Mage_Webapi_Model_Authorization_Config')
                ->disableOriginalConstructor()
                ->getMock(),
            'ruleResource' => $this->_ruleResource
        ));
    }

    /**
     * Test isEverythingAllowed method
     *
     * @dataProvider isEverythingAllowedDataProvider
     * @param array $selectedResources
     * @param bool $expectedResult
     */
    public function testIsEverythingAllowed($selectedResources, $expectedResult)
    {
        $apiRole = new Varien_Object(array(
            'role_id' => 1
        ));
        $apiRole->setIdFieldName('role_id');

        $this->_block->setApiRole($apiRole);

        $this->_ruleResource->expects($this->once())
            ->method('getResourceIdsByRole')
            ->with($apiRole->getId())
            ->will($this->returnValue($selectedResources));

        $this->assertEquals($expectedResult, $this->_block->isEverythingAllowed());
    }

    /**
     * @return array
     */
    public function isEverythingAllowedDataProvider()
    {
        return array(
            'Not Everything Allowed' => array(
                array('customer', 'customer/get'),
                false
            ),
            'Everything Allowed' => array(
                array('customer', 'customer/get', Mage_Webapi_Model_Authorization::API_ACL_RESOURCES_ROOT_ID),
                true
            )
        );
    }
}
