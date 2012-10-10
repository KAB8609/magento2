<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Customer_Block_Account_NavigationTest extends PHPUnit_Framework_TestCase
{
    public function testAddRemoveLink()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $block = Mage::getModel('Mage_Customer_Block_Account_Navigation');
        $this->assertSame(array(), $block->getLinks());
        $this->assertSame($block, $block->addLink('Name', 'some/path/index', 'Label', array('parameter' => 'value')));
        $links = $block->getLinks();
        $this->assertArrayHasKey('Name', $links);
        $this->assertInstanceOf('Varien_Object', $links['Name']);
        $this->assertSame(array(
                'name' => 'Name', 'path' => 'some/path/index', 'label' => 'Label',
                'url' => 'http://localhost/index.php/some/path/index/parameter/value/'
            ), $links['Name']->getData()
        );
        $block->removeLink('nonexistent');
        $this->assertSame($links, $block->getLinks());
        $block->removeLink('Name');
        $this->assertSame(array(), $block->getLinks());
    }
}
