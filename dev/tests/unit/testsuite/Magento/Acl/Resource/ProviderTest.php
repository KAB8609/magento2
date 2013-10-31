<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Acl\Resource;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Acl\Resource\Provider
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected  $_configReaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_treeBuilderMock;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    protected function setUp()
    {
        $this->_configReaderMock = $this->getMock('Magento\Config\ReaderInterface');
        $this->_appState = $this->getMock('Magento\App\State', null, array(), '', false);
        $this->_treeBuilderMock =
            $this->getMock('Magento\Acl\Resource\TreeBuilder', array(), array(), '', false);
        $this->_model = new \Magento\Acl\Resource\Provider(
            $this->_configReaderMock,
            $this->_treeBuilderMock,
            $this->_appState
        );
    }

    public function testGetIfAclResourcesExist()
    {
        $aclResourceConfig['config']['acl']['resources'] = array('ExpectedValue');
        $scope = 'scopeName';
        $this->_appState->setAreaCode($scope);
        $this->_configReaderMock->expects($this->once())
            ->method('read')->with($scope)->will($this->returnValue($aclResourceConfig));
        $this->_treeBuilderMock->expects($this->once())
            ->method('build')->will($this->returnValue('ExpectedResult'));
        $this->assertEquals('ExpectedResult', $this->_model->getAclResources());
    }

    public function testGetIfAclResourcesEmpty()
    {
        $scope = 'scopeName';
        $this->_appState->setAreaCode($scope);
        $this->_configReaderMock->expects($this->once())
            ->method('read')->with($scope)->will($this->returnValue(array()));
        $this->_treeBuilderMock->expects($this->never())->method('build');
        $this->assertEquals(array(), $this->_model->getAclResources());
    }
}
