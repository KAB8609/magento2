<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Core
 */
class Mage_Core_Model_Design_PackageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        $fixtureDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files';
        Mage::app()->getConfig()->getOptions()->setDesignDir($fixtureDir . DIRECTORY_SEPARATOR . 'design');
        Varien_Io_File::rmdirRecursive(Mage::app()->getConfig()->getOptions()->getMediaDir() . '/skin');
    }

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Design_Package();
        $this->_model->setDesignTheme('test/default/default', 'frontend');
    }

    public function testSetGetArea()
    {
        $this->assertEquals(Mage_Core_Model_Design_Package::DEFAULT_AREA, $this->_model->getArea());
        $this->_model->setArea('test');
        $this->assertEquals('test', $this->_model->getArea());
    }

    public function testGetPackageName()
    {
        $this->assertEquals('test', $this->_model->getPackageName());
    }

    public function testGetTheme()
    {
        $this->assertEquals('default', $this->_model->getTheme());
    }

    public function testGetSkin()
    {
        $this->assertEquals('default', $this->_model->getSkin());
    }

    public function testSetDesignTheme()
    {
        $this->_model->setDesignTheme('test/test/test', 'test');
        $this->assertEquals('test', $this->_model->getArea());
        $this->assertEquals('test', $this->_model->getPackageName());
        $this->assertEquals('test', $this->_model->getSkin());
        $this->assertEquals('test', $this->_model->getSkin());
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testSetDesignThemeException()
    {
        $this->_model->setDesignTheme('test/test');
    }

    public function testGetDesignTheme()
    {
        $this->assertEquals('test/default/default', $this->_model->getDesignTheme());
    }

    /**
     * @dataProvider getTemplateFilenameDataProvider
     */
    public function testGetTemplateFilename($file, $params)
    {
        $this->assertFileExists($this->_model->getTemplateFilename($file, $params));
    }

    /**
     * @return array
     */
    public function getTemplateFilenameDataProvider()
    {
        return array(
            array('theme_file.txt', array('_module' => 'Mage_Catalog')),
            array('Mage_Catalog::theme_file.txt', array()),
            array('Mage_Catalog::theme_file_with_2_dots..txt', array()),
            array('Mage_Catalog::theme_file.txt', array('_module' => 'Overriden_Module')),
            array('fallback.phtml', array('_package' => 'package', '_theme' => 'custom_theme')),
        );
    }
    /*
    public function testGetThemeLocaleFile()
    {
        $this->assertFileExists($this->_model->getThemeLocaleFile('translate.csv'));
        $this->assertFileExists($this->_model->getThemeLocaleFile('fallback.csv', array(
            '_package' => 'package', '_theme' => 'custom_theme'
        )));
    }
    */

    public function testGetOptimalCssUrls()
    {
        $expected = array(
            'http://localhost/pub/media/skin/frontend/test/default/default/en_US/css/styles.css',
            'http://localhost/pub/js/calendar/calendar-blue.css',
        );
        $params = array(
            'css/styles.css',
            'calendar/calendar-blue.css',
        );
        $this->assertEquals($expected, $this->_model->getOptimalCssUrls($params));
    }

    /**
     * @param array $files
     * @param array $expectedFiles
     * @dataProvider getOptimalCssUrlsMergedDataProvider
     * @magentoConfigFixture current_store dev/css/merge_css_files 1
     */
    public function testGetOptimalCssUrlsMerged($files, $expectedFiles)
    {
        $this->assertEquals($expectedFiles, $this->_model->getOptimalCssUrls($files));
    }

    public function getOptimalCssUrlsMergedDataProvider()
    {
        return array(
            array(
                array('css/styles.css', 'calendar/calendar-blue.css'),
                array('http://localhost/pub/media/skin/_merged/5594035976651f0a40d65ed577700fb5.css')
            ),
            array(
                array('css/styles.css'),
                array('http://localhost/pub/media/skin/frontend/test/default/default/en_US/css/styles.css',)
            ),
        );
    }


    public function testGetOptimalJsUrls()
    {
        $expected = array(
            'http://localhost/pub/media/skin/frontend/test/default/default/en_US/js/tabs.js',
            'http://localhost/pub/js/calendar/calendar.js',
        );
        $params = array(
            'js/tabs.js',
            'calendar/calendar.js',
        );
        $this->assertEquals($expected, $this->_model->getOptimalJsUrls($params));
    }

    /**
     * @param array $files
     * @param array $expectedFiles
     * @dataProvider getOptimalJsUrlsMergedDataProvider
     * @magentoConfigFixture current_store dev/js/merge_files 1
     */
    public function testGetOptimalJsUrlsMerged($files, $expectedFiles)
    {
        $this->assertEquals($expectedFiles, $this->_model->getOptimalJsUrls($files));
    }

    public function getOptimalJsUrlsMergedDataProvider()
    {
        return array(
            array(
                array('js/tabs.js', 'calendar/calendar.js'),
                array('http://localhost/pub/media/skin/_merged/c5a9f4afba4ff0ff979445892214fc8b.js',)
            ),
            array(
                array('calendar/calendar.js'),
                array('http://localhost/pub/js/calendar/calendar.js',)
            ),
        );
    }

    public function testGetDesignEntitiesStructure()
    {
        $expectedResult = array(
            'package_one' => array(
                'theme_one' => array(
                    'skin_one' => true,
                    'skin_two' => true
                )
            )
        );
        $this->assertSame($expectedResult, $this->_model->getDesignEntitiesStructure('design_area'));
    }

    public function testGetThemeConfig()
    {
        $frontend = $this->_model->getThemeConfig('frontend');
        $this->assertInstanceOf('Magento_Config_Theme', $frontend);
        $this->assertSame($frontend, $this->_model->getThemeConfig('frontend'));
    }

    public function testIsThemeCompatible()
    {
        $this->assertFalse($this->_model->isThemeCompatible('frontend', 'package', 'custom_theme', '1.0.0.0'));
        $this->assertTrue($this->_model->isThemeCompatible('frontend', 'package', 'custom_theme', '2.0.0.0'));
    }

    public function testGetViewConfig()
    {
        $config = $this->_model->getViewConfig();
        $this->assertInstanceOf('Magento_Config_View', $config);
        $this->assertEquals(array('var1' => 'value1', 'var2' => 'value2'), $config->getVars('Namespace_Module'));
    }
}
