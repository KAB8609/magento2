<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Acl_Loader_DefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Acl\Loader\DefaultLoader
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new \Magento\Acl\Loader\DefaultLoader();
    }

    public function testPopulateAclDoesntChangeAclObject()
    {
        $aclMock = $this->getMock('Magento\Acl');
        $aclMock->expects($this->never())->method('addRole');
        $aclMock->expects($this->never())->method('addResource');
        $aclMock->expects($this->never())->method('allow');
        $aclMock->expects($this->never())->method('deny');
        $this->_model->populateAcl($aclMock);
    }
}
