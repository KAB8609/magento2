<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Backend\Block\Page\System\Config\Robots\Reset
 */
namespace Magento\Backend\Block\Page\System\Config\Robots;

class ResetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\Page\System\Config\Robots\Reset
     */
    private $_resetRobotsBlock;

    /**
     * @var \Magento\Page\Helper\Robots|PHPUnit_Framework_MockObject_MockObject
     */
    private $_mockRobotsHelper;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_mockRobotsHelper = $this->getMock('Magento\Page\Helper\Robots',
            array('getRobotsDefaultCustomInstructions'), array(), '', false, false
        );

        $this->_resetRobotsBlock = $objectManagerHelper->getObject(
            'Magento\Backend\Block\Page\System\Config\Robots\Reset',
            array(
                'pageRobots' => $this->_mockRobotsHelper,
                'coreData' => $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false),
                'application' => $this->getMock('Magento\Core\Model\App', array(), array(), '', false),
            )
        );

        $coreRegisterMock = $this->getMock('Magento\Core\Model\Registry');
        $coreRegisterMock->expects($this->any())
            ->method('registry')
            ->with('_helper/\Magento\Page\Helper\Robots')
            ->will($this->returnValue($this->_mockRobotsHelper));

        $objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')->getMock();
        $objectManagerMock->expects($this->any())
            ->method('get')
            ->with('Magento\Core\Model\Registry')
            ->will($this->returnValue($coreRegisterMock));
        \Magento\App\ObjectManager::setInstance($objectManagerMock);
    }

    /**
     * @covers \Magento\Backend\Block\Page\System\Config\Robots\Reset::getRobotsDefaultCustomInstructions
     */
    public function testGetRobotsDefaultCustomInstructions()
    {
        $expectedInstructions = 'User-agent: *';
        $this->_mockRobotsHelper->expects($this->once())
            ->method('getRobotsDefaultCustomInstructions')
            ->will($this->returnValue($expectedInstructions));
        $this->assertEquals($expectedInstructions, $this->_resetRobotsBlock->getRobotsDefaultCustomInstructions());
    }
}