<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Block;

class SiteVerificationTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GoogleShopping\Block\SiteVerification */
    protected $_object;

    /** @var \Magento\GoogleShopping\Model\Config */
    protected $_config;

    protected function setUp()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $layout = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $coreHelper = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $coreHelper->expects($this->any())
            ->method('escapeHtml')->with('Valor & Honor')->will($this->returnValue('Valor &amp; Honor'));
        $helperFactory = $this->getMockBuilder('Magento\Core\Model\Factory\Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($coreHelper));
        $layout->expects($this->any())
            ->method('helper')->with('Magento\Core\Helper\Data')->will($this->returnValue($coreHelper));
        $context = $objectHelper->getObject('Magento\Core\Block\Context', array(
            'eventManager' => $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false),
            'layout' => $layout,
            'helperFactory' => $helperFactory
        ));
        $this->_config = $this->getMock('Magento\GoogleShopping\Model\Config', array(), array(), '', false);
        $this->_block = new \Magento\GoogleShopping\Block\SiteVerification($context, $this->_config);
    }

    public function testToHtmlWithContent()
    {
        $this->_config->expects($this->once())
            ->method('getConfigData')->with('verify_meta_tag')->will($this->returnValue('Valor & Honor'));
        $this->assertEquals(
            '<meta name="google-site-verification" content="Valor &amp; Honor"/>',
            $this->_block->toHtml()
        );
    }

    public function testToHtmlWithoutContent()
    {
        $this->_config->expects($this->once())
            ->method('getConfigData')->with('verify_meta_tag')->will($this->returnValue(''));
        $this->assertEquals('', $this->_block->toHtml());
    }
}
