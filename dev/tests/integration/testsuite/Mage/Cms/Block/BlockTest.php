<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Cms_Block_BlockTest extends PHPUnit_Framework_TestCase
{
    /**
     * magentoDataFixture Mage/Cms/_files/block.php
     * magentoDataFixture Mage/Core/_files/variable.php
     * @magentoConfigFixture current_store web/unsecure/base_url http://example.com/
     * @magentoConfigFixture current_store web/unsecure/base_link_url http://example.com/
     */
    public function testToHtml()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + fixture');

        $cmsBlock = Mage::getModel('Mage_Cms_Model_Block');
        $cmsBlock->load('fixture_block', 'identifier');
        $block = Mage::app()->getLayout()->createBlock('Mage_Cms_Block_Block');
        $block->setBlockId($cmsBlock->getId());
        $result = $block->toHtml();
        $this->assertContains('<a href="http://example.com/', $result);
        $this->assertContains('<p>Config value: "http://example.com/".</p>', $result);
        $this->assertContains('<p>Custom variable: "HTML Value".</p>', $result);
    }
}
