<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceSetupMock;

    /**
     * @var \Magento\App\Updater
     */
    protected $_model;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock('Magento\App\Updater\SetupFactory', array(), array(), '', false);
        $this->_appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->_moduleListMock = $this->getMock('Magento\App\ModuleListInterface');
        $this->_resourceResolver = $this->getMock('Magento\App\Module\ResourceResolverInterface');
        $this->_resourceSetupMock = $this->getMock('Magento\Catalog\Model\Resource\Setup', array(), array(), '', false);

        $moduleList = array('Test_Module' => array());
        $this->_moduleListMock->expects($this->any())
            ->method('getModules')
            ->will($this->returnValue($moduleList));

        $resourceList = array('catalog_setup');
        $this->_resourceResolver->expects($this->any())
            ->method('getResourceList')
            ->with('Test_Module')
            ->will($this->returnValue($resourceList));

        $this->_factoryMock->expects($this->any())
            ->method('create')
            ->with('catalog_setup', 'Test_Module')
            ->will($this->returnValue($this->_resourceSetupMock));

        $this->_model = new \Magento\App\Updater(
            $this->_factoryMock,
            $this->_appStateMock,
            $this->_moduleListMock,
            $this->_resourceResolver,
            true
        );
    }

    /**
     * @covers \Magento\App\Updater::updateScheme
     */
    public function testUpdateSchemeWithUpdateSkip()
    {
        $this->_appStateMock->expects($this->once())
            ->method('isInstalled')
            ->will($this->returnValue(true));

        $this->_appStateMock->expects($this->never())
            ->method('setUpdateMode');

        $this->_model->updateScheme();
    }

    /**
     * @covers \Magento\App\Updater::updateScheme
     */
    public function testUpdateScheme()
    {
        $this->_appStateMock->expects($this->once())
            ->method('isInstalled')
            ->will($this->returnValue(false));

        $this->_appStateMock->expects($this->at(1))
            ->method('setUpdateMode')
            ->with(true);

        $this->_appStateMock->expects($this->at(2))
            ->method('setUpdateMode')
            ->with(false);

        $this->_resourceSetupMock->expects($this->once())
            ->method('applyUpdates');
        $this->_resourceSetupMock->expects($this->once())
            ->method('getCallAfterApplyAllUpdates')
            ->will($this->returnValue(true));
        $this->_resourceSetupMock->expects($this->once())
            ->method('afterApplyAllUpdates');

        $this->_model->updateScheme();
    }

    /**
     * @covers \Magento\App\Updater::updateData
     */
    public function testUpdateData()
    {
        $this->_resourceSetupMock->expects($this->never())
            ->method('applyDataUpdates');

        $this->_model->updateData();
    }
}
