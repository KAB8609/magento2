<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Page\Block\Link;

class CurrentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_defaultPathMock;

    protected function setUp()
    {
        $this->_urlBuilderMock = $this->getMock('\Magento\UrlInterface');
        $this->_requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->_coreHelperMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $this->_contextMock = $this->getMock('Magento\Core\Block\Template\Context', array(), array(), '', false);
        $this->_defaultPathMock = $this->getMock('\Magento\App\DefaultPathInterface');
        $this->_contextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->_urlBuilderMock));
        $this->_contextMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->_requestMock));
    }

    public function testGetUrl()
    {
        $path = 'test/path';
        $url = 'http://example.com/asdasd';

        $this->_urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($path)
            ->will($this->returnValue($url));

        $link = new \Magento\Page\Block\Link\Current(
            $this->_coreHelperMock,
            $this->_contextMock,
            $this->_defaultPathMock
        );

        $link->setPath($path);
        $this->assertEquals($url, $link->getHref());
    }


    public function testIsCurrentIfIsset()
    {
        $link = new \Magento\Page\Block\Link\Current(
            $this->_coreHelperMock,
            $this->_contextMock,
            $this->_defaultPathMock
        );
        $link->setCurrent(true);
        $this->assertTrue($link->IsCurrent());
    }

    public function testIsCurrent()
    {
        $path = 'test/path';
        $url = 'http://example.com/a/b';

        $this->_requestMock->expects($this->once())->method('getModuleName')->will($this->returnValue('a'));
        $this->_requestMock->expects($this->once())->method('getControllerName')->will($this->returnValue('b'));
        $this->_requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('d'));
        $this->_defaultPathMock->expects($this->atLeastOnce())
            ->method('getPart')
            ->will($this->returnValue('d'));

        $this->_urlBuilderMock->expects($this->at(0))->method('getUrl')->with($path)->will($this->returnValue($url));
        $this->_urlBuilderMock->expects($this->at(1))
            ->method('getUrl')
            ->with('a/b')
            ->will($this->returnValue($url));

        $link = new \Magento\Page\Block\Link\Current(
            $this->_coreHelperMock,
            $this->_contextMock,
            $this->_defaultPathMock
        );
        $link->setPath($path);
        $this->assertTrue($link->isCurrent());
    }

    public function testIsCurrentFalse()
    {
        $this->_urlBuilderMock->expects($this->at(0))->method('getUrl')->will($this->returnValue('1'));
        $this->_urlBuilderMock->expects($this->at(1))->method('getUrl')->will($this->returnValue('2'));

        $link = new \Magento\Page\Block\Link\Current(
            $this->_coreHelperMock,
            $this->_contextMock,
            $this->_defaultPathMock
        );
        $this->assertFalse($link->isCurrent());
    }
}
