<?php
/**
 * Test class for Mage_Webapi_Model_Authorization_Loader_Resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Authorization_Loader_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Authorization_Loader_Resource
     */
    protected $_model;

    /**
     * @var Magento_Acl
     */
    protected $_acl;

    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceProvider;

    /**
     * Set up before test.
     */
    protected function setUp()
    {
        $fixturePath = __DIR__ . '/../../_files/';
        $this->_helper = new Magento_Test_Helper_ObjectManager($this);

        $resource = new Magento_Acl_Resource('test resource');

        /** @var $resourceFactory Magento_Acl_ResourceFactory */
        $resourceFactory = $this->getMock('Magento_Acl_ResourceFactory',
            array('createResource'), array(), '', false);
        $resourceFactory->expects($this->any())
            ->method('createResource')
            ->will($this->returnValue($resource));

        $this->_resourceProvider = $this->getMock('Mage_Webapi_Model_Acl_Resource_ProviderInterface');
        $this->_resourceProvider->expects($this->once())
            ->method('getAclResources')
            ->will($this->returnValue(include $fixturePath . 'acl.php'));

        $this->_model = $this->_helper->getObject('Mage_Webapi_Model_Authorization_Loader_Resource', array(
            'resourceFactory' => $resourceFactory,
            'resourceProvider' => $this->_resourceProvider,
        ));

        $this->_acl = $this->getMock(
            'Magento_Acl', array('has', 'addResource', 'deny', 'getResources'), array(), '', false
        );
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Resource::populateAcl.
     */
    public function testPopulateAcl()
    {
        $aclFilePath = __DIR__ . DIRECTORY_SEPARATOR .  '..' . DIRECTORY_SEPARATOR .  '..'
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'acl.xml';
        $aclDom = new DOMDocument();
        $aclDom->loadXML(file_get_contents($aclFilePath));
        $domConverter = new Mage_Webapi_Model_Acl_Resource_Config_Converter_Dom();
        $aclResourceConfig = $domConverter->convert($aclDom);

        $this->_resourceProvider->expects($this->once())
            ->method('getAclVirtualResources')
            ->will($this->returnValue($aclResourceConfig['config']['mapping']));

        $this->_acl->expects($this->once())
            ->method('getResources')
            ->will($this->returnValue(array('customer/get', 'customer/create')));
        $this->_acl->expects($this->exactly(2))
            ->method('deny')
            ->with(null, $this->logicalOr('customer/get', 'customer/create'));
        $this->_acl->expects($this->exactly(2))
            ->method('has')
            ->with($this->logicalOr('customer/get', 'customer/list'))
            ->will($this->returnValueMap(array(
                array('customer/get', true),
                array('customer/list', false)
            )));
        $this->_acl->expects($this->exactly(7))
            ->method('addResource');

        $this->_model->populateAcl($this->_acl);
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Resource::populateAcl with invalid Virtual resources DOM.
     */
    public function testPopulateAclWithInvalidDOM()
    {
        $this->_resourceProvider->expects($this->once())
            ->method('getAclVirtualResources')
            ->will($this->returnValue(array()));

        $this->_acl->expects($this->once())
            ->method('getResources')
            ->will($this->returnValue(array('customer/get', 'customer/list')));
        $this->_acl->expects($this->exactly(2))
            ->method('deny')
            ->with(null, $this->logicalOr('customer/get', 'customer/list'));

        $this->_model->populateAcl($this->_acl);
    }
}
