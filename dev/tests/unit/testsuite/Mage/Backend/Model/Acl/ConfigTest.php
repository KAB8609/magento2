<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Acl_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Acl_Config
     */
    protected  $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    public function setUp()
    {
        $this->_readerMock = $this->getMock('Magento_Acl_Config_Reader', array(), array(), '', false);
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_cacheMock  = $this->getMock('Mage_Core_Model_Cache_Type_Config', array(), array(), '', false);

        $this->_model = new Mage_Backend_Model_Acl_Config($this->_configMock, $this->_cacheMock,
            $this->getMock('Mage_Core_Model_Config_Modules_Reader', array(), array(), '', false, false)
        );
    }

    public function testGetAclResourcesWhenCacheLoadCorruptedValue()
    {
        $originalAclResources = new DOMDocument();
        $originalAclResources->loadXML('<?xml version="1.0" encoding="utf-8"?><config><acl></acl></config>');

        $this->_configMock->expects($this->once())->method('getModelInstance')
            ->with($this->equalTo('Magento_Acl_Config_Reader'))
            ->will($this->returnValue($this->_readerMock));

        $this->_cacheMock->expects($this->once())->method('load')
            ->with($this->equalTo(Mage_Backend_Model_Acl_Config::CACHE_ID))
            ->will($this->returnValue(1234));

        $this->_cacheMock->expects($this->once())->method('save')
            ->with($this->equalTo($originalAclResources->saveXML()));

        $this->_readerMock->expects($this->once())->method('getAclResources')
            ->will($this->returnValue($originalAclResources));

        $this->_model->getAclResources();
    }

    public function testGetAclResourcesWithEnabledAndCleanedUpCache()
    {
        $originalAclResources = new DOMDocument();
        $originalAclResources->loadXML(
            '<?xml version="1.0" encoding="utf-8"?>'
            . '<config>'
                . '<acl>'
                    . '<resources>'
                        . '<resource id="res"></resource>'
                    . '</resources>'
                . '</acl>'
            . '</config>'
        );

        $this->_configMock->expects($this->once())->method('getModelInstance')
            ->with($this->equalTo('Magento_Acl_Config_Reader'))
            ->will($this->returnValue($this->_readerMock));

        $this->_cacheMock->expects($this->once())->method('load')
            ->with($this->equalTo(Mage_Backend_Model_Acl_Config::CACHE_ID))
            ->will($this->returnValue(null));

        $this->_cacheMock->expects($this->once())->method('save')
            ->with($this->equalTo($originalAclResources->saveXML()));

        $this->_readerMock->expects($this->once())->method('getAclResources')
            ->will($this->returnValue($originalAclResources));

        $aclResources = $this->_model->getAclResources();

        $this->assertInstanceOF('DOMNodeList', $aclResources);
        $this->assertEquals(1, $aclResources->length);
        $this->assertEquals('res', $aclResources->item(0)->getAttribute('id'));
    }

    public function testGetAclResourcesWithEnabledAndGeneratedCache()
    {
        $this->_configMock->expects($this->never())->method('getModelInstance');

        $this->_cacheMock->expects($this->exactly(2))->method('load')
            ->with($this->equalTo(Mage_Backend_Model_Acl_Config::CACHE_ID))
            ->will($this->returnValue('<?xml version="1.0" encoding="utf-8"?><config><acl></acl></config>'));

        $this->_cacheMock->expects($this->never())->method('save');
        $this->_readerMock->expects($this->never())->method('getAclResources');

        $firstCall = $this->_model->getAclResources();
        $secondCall = $this->_model->getAclResources();

        $this->assertNotEmpty($firstCall);
        $this->assertNotEmpty($secondCall);

        $this->assertEquals($firstCall, $secondCall);
    }
}

