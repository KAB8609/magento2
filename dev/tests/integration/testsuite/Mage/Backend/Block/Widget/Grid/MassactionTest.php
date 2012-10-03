<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_Grid_MassactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Block_Widget_Grid_Massaction
     */
    protected $_block;

    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    public static function setUpBeforeClass()
    {
        /* Point application to predefined layout fixtures */
        Mage::getConfig()->setOptions(array(
            'design_dir' => realpath( __DIR__ . '/../../_files/design'),
        ));
        Mage::getDesign()->setDesignTheme('test/default/default', 'adminhtml');

        /* Disable loading and saving layout cache */
        Mage::app()->getCacheInstance()->banUse('layout');
    }

    protected function setUp()
    {
        $this->_layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
        $this->_layout->getUpdate()->load('layout_test_grid_handle');
        $this->_layout->generateXml();
        $this->_layout->generateElements();

        $this->_block = $this->_layout->getBlock('admin.test.grid.massaction');
    }

    protected function tearDown()
    {
        unset($this->_layout);
        unset($this->_block);
    }

    /**
     * @covers getItems
     * @covers getCount
     * @covers getItemsJson
     * @covers isAvailable
     */
    public function testMassactionDefaultValues()
    {
        $blockEmpty = new Mage_Backend_Block_Widget_Grid_Massaction();
        $this->assertEmpty($blockEmpty->getItems());
        $this->assertEquals(0, $blockEmpty->getCount());
        $this->assertSame('[]', $blockEmpty->getItemsJson());

        $this->assertFalse($blockEmpty->isAvailable());
    }

    public function testJavascript()
    {
        $javascript = $this->_block->getJavaScript();

        $expectedItem1 =  '#"option_id1":{"label":"Option One",'
            . '"url":"http:\\\/\\\/localhost\\\/index\.php\\\/key\\\/([\w\d]+)\\\/",'
            . '"complete":"Test","id":"option_id1"}#';
        $this->assertRegExp($expectedItem1, $javascript);

        $expectedItem2 =  '#"option_id2":{"label":"Option Two",'
            . '"url":"http:\\\/\\\/localhost\\\/index\.php\\\/key\\\/([\w\d]+)\\\/",'
            . '"confirm":"Are you sure\?","id":"option_id2"}#';
        $this->assertRegExp($expectedItem2, $javascript);
    }

    public function testJavascriptWithAddedItem()
    {
        $input = array(
            'id' => 'option_id3',
            'label' => 'Option Three',
            'url' => '*/*/option3',
            'block_name' => 'admin.test.grid.massaction.option3'
        );
        $expected = '#"option_id3":{"id":"option_id3","label":"Option Three",'
            . '"url":"http:\\\/\\\/localhost\\\/index\.php\\\/key\\\/([\w\d]+)\\\/",'
            . '"block_name":"admin.test.grid.massaction.option3"}#';

        $this->_block->addItem($input['id'], $input);
        $this->assertRegExp($expected, $this->_block->getJavaScript());
    }

    public function testItemsCount()
    {
        $this->assertEquals(2, count($this->_block->getItems()));
        $this->assertEquals(2, $this->_block->getCount());
    }

    /**
     * @param $itemId
     * @param $expectedItem
     * @dataProvider itemsDataProvider
     */
    public function testItems($itemId, $expectedItem)
    {
        $items = $this->_block->getItems();
        $this->assertArrayHasKey($itemId, $items);

        $actualItem = $items[$itemId];
        $this->assertEquals($expectedItem['id'], $actualItem->getId());
        $this->assertEquals($expectedItem['label'], $actualItem->getLabel());
        $this->assertRegExp($expectedItem['url'], $actualItem->getUrl());
        $this->assertEquals($expectedItem['selected'], $actualItem->getSelected());
        $this->assertEquals($expectedItem['blockname'], $actualItem->getBlockName());
    }

    public function itemsDataProvider()
    {
        return array(
            array(
                'option_id1',
                array(
                    'id' => 'option_id1',
                    'label' => 'Option One',
                    'url' => '#http:\/\/localhost\/index\.php\/key\/([\w\d]+)\/#',
                    'selected' => false,
                    'blockname' => ''
                )
            ),
            array(
                'option_id2',
                array(
                    'id' => 'option_id2',
                    'label' => 'Option Two',
                    'url' => '#http:\/\/localhost\/index\.php\/key\/([\w\d]+)\/#',
                    'selected' => false,
                    'blockname' => ''
                )
            )
        );
    }

    public function testGridContainsMassactionColumn()
    {
        $gridBlock = $this->_layout->getBlock('admin.test.grid');
        $this->assertRegExp(
            '#<th><span class="head-massaction"><select name="massaction" id="([\w\d_]+)_filter_massaction" '
                . 'class="no-changes"><option value="">Any</option><option value="1">Yes</option>'
                . '<option value="0">No</option></select></span></th>#',
            $gridBlock->toHtml()
        );
    }

}
