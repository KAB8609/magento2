<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Theme_Block_Adminhtml_System_Design_Theme_TabAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_TabAbstract
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass(
            'Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_TabAbstract',
            array(
                $this->getMock('Magento_Data_Form_Factory', array(), array(), '', false),
                $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false),
                $this->getMock('Magento_Backend_Block_Template_Context', array(), array(), '', false),
                $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false),
                $this->getMock('Magento_ObjectManager', array(), array(), '', false)
            ),
            '',
            true,
            false,
            true,
            array('_getCurrentTheme', 'getTabLabel')
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testGetTabTitle()
    {
        $label = 'test label';
        $this->_model
            ->expects($this->once())
            ->method('getTabLabel')
            ->will($this->returnValue($label));
        $this->assertEquals($label, $this->_model->getTabTitle());
    }

    /**
     * @dataProvider canShowTabDataProvider
     * @param bool $isVirtual
     * @param int $themeId
     * @param bool $result
     */
    public function testCanShowTab($isVirtual, $themeId, $result)
    {
        $themeMock = $this->getMock('Magento_Core_Model_Theme', array('isVirtual', 'getId'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('isVirtual')
            ->will($this->returnValue($isVirtual));

        $themeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($themeId));

        $this->_model->expects($this->any())
            ->method('_getCurrentTheme')
            ->will($this->returnValue($themeMock));

        if ($result === true) {
            $this->assertTrue($this->_model->canShowTab());
        } else {
            $this->assertFalse($this->_model->canShowTab());
        }
    }

    /**
     * @return array
     */
    public function canShowTabDataProvider()
    {
        return array(
            array(true, 1, true),
            array(true, 0, false),
            array(false, 1, false),
        );
    }

    public function testIsHidden()
    {
        $this->assertFalse($this->_model->isHidden());
    }
}
