<?php
/**
 * Abstract test class for Magento_Webapi_Model_Resource_Acl. Added to eliminate copy-paste.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Resource_Acl_TestAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_helper;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Resource|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var Magento_DB_Adapter_Pdo_Mysql|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapter;

    protected function setUp()
    {
        $this->_helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();
    }
}