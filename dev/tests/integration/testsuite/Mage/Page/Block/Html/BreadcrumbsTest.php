<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Page
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Block_Html_BreadcrumbsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Page_Block_Html_Breadcrumbs
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Page_Block_Html_Breadcrumbs');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testAddCrumb()
    {
        $this->assertEmpty($this->_block->toHtml());
        $info = array(
            'label' => 'test label',
            'title' => 'test title',
            'link'  => 'test link',
        );
        $this->_block->addCrumb('test', $info);
        $html = $this->_block->toHtml();
        $this->assertContains('test label', $html);
        $this->assertContains('test title', $html);
        $this->assertContains('test link', $html);
    }

    public function testGetCacheKeyInfo()
    {
        $crumbs = array(
            'test' => array(
                'label'    => 'test label',
                'title'    => 'test title',
                'link'     => 'test link',
            )
        );
        foreach ($crumbs as $crumbName => &$crumb) {
            $this->_block->addCrumb($crumbName, $crumb);
            $crumb += array(
                'first'    => null,
                'last'     => null,
                'readonly' => null,
            );
        }

        $cacheKeyInfo = $this->_block->getCacheKeyInfo();
        $crumbsFromCacheKey = unserialize(base64_decode($cacheKeyInfo['crumbs']));
        $this->assertEquals($crumbs, $crumbsFromCacheKey);
    }
}
