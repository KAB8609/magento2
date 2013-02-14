<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Page_System_Config_Robots_Reset
 */
class Mage_Adminhtml_Block_Page_System_Config_Robots_ResetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Block_Page_System_Config_Robots_Reset
     */
    private $_resetRobotsBlock;

    /**
     * @var Mage_Page_Helper_Robots|PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockRobotsHelper;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_resetRobotsBlock = $objectManagerHelper->getBlock(
            'Mage_Adminhtml_Block_Page_System_Config_Robots_Reset',
            array(
                'application' => $this->getMock('Mage_Core_Model_App', array(), array(), '', false),
                'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false)
            )
        );
        $this->_mockRobotsHelper = $this->getMock('Mage_Page_Helper_Robots',
            array('getRobotsDefaultCustomInstructions'), array(), '', false, false
        );
        Mage::register('_helper/Mage_Page_Helper_Robots', $this->_mockRobotsHelper);
    }

    protected function tearDown()
    {
        Mage::unregister('_helper/Mage_Page_Helper_Robots');
    }

    /**
     * @covers Mage_Adminhtml_Block_Page_System_Config_Robots_Reset::getRobotsDefaultCustomInstructions
     */
    public function testGetRobotsDefaultCustomInstructions()
    {
        $expectedInstructions = 'User-agent: *';
        $this->_mockRobotsHelper
            ->expects($this->once())
            ->method('getRobotsDefaultCustomInstructions')
            ->will($this->returnValue($expectedInstructions));
        $this->assertEquals($expectedInstructions, $this->_resetRobotsBlock->getRobotsDefaultCustomInstructions());
    }
}
