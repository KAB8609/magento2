<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Super\Config;

class MatrixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object under test
     *
     * @var \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Super\Config_Matrix
     */
    protected $_block;

    /** @var \Magento\Core\Model\App|\PHPUnit_Framework_MockObject_MockObject */
    protected $_application;

    /** @var \Magento\Core\Model\LocaleInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_locale;

    protected function setUp()
    {
        $this->_application = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $this->_locale = $this->getMock('Magento\Core\Model\LocaleInterface', array(), array(), '', false);
        $data = array(
            'app' => $this->_application,
            'locale' => $this->_locale,
            'formFactory' => $this->getMock('Magento\Data\FormFactory', array(), array(), '', false),
            'productFactory' => $this->getMock('Magento\Catalog\Model\ProductFactory', array(), array(), '', false),
        );
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_object = $helper->getObject('Magento\Backend\Block\System\Config\Form', $data);
        $this->_block = $helper->getObject(
            'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Super\Config\Matrix', $data
        );
    }

    public function testRenderPrice()
    {
        $this->_application->expects($this->once())
            ->method('getBaseCurrencyCode')->with()->will($this->returnValue('USD'));
        $currency = $this->getMock('Zend_Currency', array(), array(), '', false);
        $currency->expects($this->once())
            ->method('toCurrency')->with('100.0000')->will($this->returnValue('$100.00'));
        $this->_locale->expects($this->once())
            ->method('currency')->with('USD')->will($this->returnValue($currency));
        $this->assertEquals('$100.00', $this->_block->renderPrice(100));
    }
}