<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Layout
     */
    protected $_layoutMock;

    protected function setUp()
    {
        $this->_layoutMock = $this->getMockBuilder('Magento\View\Layout')->getMockForAbstractClass();
    }

    public function testPrepareLayout()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $grid = $this->getMock('Magento\Adminhtml\Block\Catalog\Product\Widget\Chooser', array('getSelectedProducts'),
            array(), '', false);
        $grid->expects($this->once())->method('getSelectedProducts')->will($this->returnValue(array('product1')));
        $arguments = array(
            'data' => array(
                'grid_block' => $grid,
                'callback' => 'getSelectedProducts',
                'input_element_name' => 'selected_products_input',
                'reload_param_name' => 'selected_products_param'
            )
        );

        $block = $objectManagerHelper->getObject('Magento\Backend\Block\Widget\Grid\Serializer', $arguments);
        $block->setLayout($this->_layoutMock);

        $this->assertEquals($grid, $block->getGridBlock());
        $this->assertEquals(array('product1'), $block->getSerializeData());
        $this->assertEquals('selected_products_input', $block->getInputElementName());
        $this->assertEquals('selected_products_param', $block->getReloadParamName());
    }
}
