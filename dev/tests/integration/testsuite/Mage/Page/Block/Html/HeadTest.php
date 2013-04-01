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

class Mage_Page_Block_Html_HeadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Page_Block_Html_Head
     */
    private $_block = null;

    protected function setUp()
    {
        Mage::getDesign()->setDesignTheme('default/demo', 'frontend');
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Page_Block_Html_Head');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Parameter 'file' must not be empty
     * @magentoAppIsolation enabled
     */
    public function testAddCssException()
    {
        $this->_block->addCss('');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetCssJsHtml()
    {
        $this->_block->addJs('zero.js', '', null, 'nonexisting_condition')
            ->addJs('varien/js.js')
            ->addJs('Mage_Bundle::bundle.js')
            ->addCss('tiny_mce/themes/advanced/skins/default/ui.css')
            ->addCss('css/styles.css', 'media="print"')
            ->addRss('RSS Feed', 'http://example.com/feed.xml')
            ->addLinkRel('next', 'http://example.com/page1.html')
            ->addJs('varien/form.js', '', 'lt IE 7')
        ;
        $this->assertEquals(
            '<script type="text/javascript" src="http://localhost/pub/lib/varien/js.js"></script>' . "\n"
            . '<script type="text/javascript" '
            . 'src="http://localhost/pub/static/frontend/default/demo/en_US/Mage_Bundle/bundle.js">'
            . '</script>' . "\n"
            . '<link rel="stylesheet" type="text/css" media="all"'
            . ' href="http://localhost/pub/lib/tiny_mce/themes/advanced/skins/default/ui.css" />' . "\n"
            . '<link rel="stylesheet" type="text/css" media="print" '
                . 'href="http://localhost/pub/static/frontend/default/demo/en_US/css/styles.css" />'
                . "\n"
            . '<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="http://example.com/feed.xml" />'
                . "\n"
            . '<link rel="next" href="http://example.com/page1.html" />' . "\n"
            . '<!--[if lt IE 7]>' . "\n"
            . '<script type="text/javascript" src="http://localhost/pub/lib/varien/form.js"></script>' . "\n"
            . '<![endif]-->' . "\n",
            $this->_block->getCssJsHtml()
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetCssJsHtmlBadLink()
    {
        $this->_block->addCss('not_exist_folder/wrong_bad_file.xyz')
            ->addJs('not_exist_folder/wrong_bad_file2.xyz');

        $this->assertEquals('<link rel="stylesheet" type="text/css" media="all"'
                . ' href="http://localhost/index.php/core/index/notfound" />' . "\n"
                . '<script type="text/javascript" src="http://localhost/index.php/core/index/notfound"></script>'
                . "\n",
            $this->_block->getCssJsHtml());
    }

    /**
     * Both existing and non-existent JS and CSS links are specified
     * @magentoAppIsolation enabled
     */
    public function testGetCssJsHtmlMixedLinks()
    {
        $this->_block->addJs('varien/js.js')
            ->addJs('varien/form.js', '', 'lt IE 7')
            ->addCss('not_exist_folder/wrong_bad_file.xyz')
            ->addCss('css/styles.css', 'media="print"')
            ->addJs('not_exist_folder/wrong_bad_file2.xyz');

        $this->assertEquals('<script type="text/javascript" src="http://localhost/pub/lib/varien/js.js"></script>'
            . "\n" . '<script type="text/javascript" src="http://localhost/index.php/core/index/notfound"></script>'
            . "\n" . '<!--[if lt IE 7]>' . "\n"
            . '<script type="text/javascript" src="http://localhost/pub/lib/varien/form.js"></script>' . "\n"
            . '<![endif]-->' . "\n"
            . '<link rel="stylesheet" type="text/css" media="all"'
            . ' href="http://localhost/index.php/core/index/notfound" />' . "\n"
            . '<link rel="stylesheet" type="text/css" media="print"'
            . ' href="http://localhost/pub/static/frontend/default/demo/en_US/css/styles.css" />'
            . "\n", $this->_block->getCssJsHtml());
    }

    /**
     * Test getRobots default value
     * @magentoAppIsolation enabled
     */
    public function testGetRobotsDefaultValue()
    {
        $this->assertEquals('INDEX,FOLLOW', $this->_block->getRobots());
    }

    /**
     * Test getRobots
     *
     * @magentoConfigFixture default_store design/search_engine_robots/default_robots INDEX,NOFOLLOW
     * @magentoAppIsolation enabled
     */
    public function testGetRobots()
    {
        $this->assertEquals('INDEX,NOFOLLOW', $this->_block->getRobots());
    }
}
