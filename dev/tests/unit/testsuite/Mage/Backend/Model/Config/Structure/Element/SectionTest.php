
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

class Mage_Backend_Model_Config_Structure_Element_SectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Element_Section
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_iteratorMock;

    protected function setUp()
    {
        $this->_iteratorMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Element_Iterator_Field', array(), array(), '', false
        );
        $this->_factoryHelperMock = $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false);
        $this->_applicationMock = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $this->_authorizationMock = $this->getMock('Mage_Core_Model_Authorization', array(), array(), '', false);

        $this->_model = new Mage_Backend_Model_Config_Structure_Element_Section(
            $this->_factoryHelperMock, $this->_applicationMock, $this->_iteratorMock, $this->_authorizationMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_iteratorMock);
        unset($this->_factoryHelperMock);
        unset($this->_applicationMock);
        unset($this->_authorizationMock);
    }

    public function testIsAllowedReturnsFalseIfNoResourceIsSpecified()
    {
        $this->assertFalse($this->_model->isAllowed());
    }

    public function testIsAllowedReturnsTrueIfResourcesIsValidAndAllowed()
    {
        $this->_authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('someResource')
            ->will($this->returnValue(true));

        $this->_model->setData(array('resource' => 'someResource'), 'store');
        $this->assertTrue($this->_model->isAllowed());
    }

    public function testIsVisibleFirstChecksIfSectionIsAllowed()
    {
        $this->_applicationMock->expects($this->never())->method('isSingleStoreMode');
        $this->assertFalse($this->_model->isVisible());
    }

    public function testIsVisibleProceedsWithVisibilityCheckIfSectionIsAllowed()
    {
        $this->_authorizationMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->_applicationMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->_model->setData(array('resource' => 'Mage_Adminhtml::all'), 'scope');
        $this->_model->isVisible();
    }
}


