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
    protected $_urlBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_defaultPathMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_urlBuilderMock = $this->getMock('\Magento\UrlInterface');
        $this->_requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->_defaultPathMock = $this->getMock('\Magento\App\DefaultPathInterface');
    }

    public function testGetUrl()
    {
        $path = 'test/path';
        $url = 'http://example.com/asdasd';

        $this->_urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($path)
            ->will($this->returnValue($url));

        /** @var \Magento\Page\Block\Link\Current $link */
        $link = $this->_objectManager->getObject(
            '\Magento\Page\Block\Link\Current',
            array('urlBuilder' => $this->_urlBuilderMock)
        );

        $link->setPath($path);
        $this->assertEquals($url, $link->getHref());
    }


    public function testIsCurrentIfIsset()
    {
        /** @var \Magento\Page\Block\Link\Current $link */
        $link = $this->_objectManager->getObject('\Magento\Page\Block\Link\Current');
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

        $this->_requestMock->expects($this->once())->method('getControllerName')->will($this->returnValue('b'));
        /** @var \Magento\Page\Block\Link\Current $link */
        $link = $this->_objectManager->getObject('\Magento\Page\Block\Link\Current',
            array(
                'urlBuilder' => $this->_urlBuilderMock,
                'request' => $this->_requestMock,
                'defaultPath' => $this->_defaultPathMock
            ));
        $link->setPath($path);
        $this->assertTrue($link->isCurrent());
    }

    public function testIsCurrentFalse()
    {
        $this->_urlBuilderMock->expects($this->at(0))->method('getUrl')->will($this->returnValue('1'));
        $this->_urlBuilderMock->expects($this->at(1))->method('getUrl')->will($this->returnValue('2'));


        /** @var \Magento\Page\Block\Link\Current $link */
        $link = $this->_objectManager->getObject('\Magento\Page\Block\Link\Current',
            array(
                'urlBuilder' => $this->_urlBuilderMock,
                'request' => $this->_requestMock
            ));
        $this->assertFalse($link->isCurrent());
    }
}