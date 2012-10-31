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

class Mage_Core_Model_Design_PackagePublicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Path to the public directory for skin files
     *
     * @var string
     */
    protected static $_skinPublicDir;

    /**
     * Path for temporary fixture files. Used to test publishing changed files.
     *
     * @var string
     */
    protected static $_fixtureTmpDir;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        self::$_skinPublicDir = Mage::app()->getConfig()->getOptions()->getMediaDir() . '/theme';
        self::$_fixtureTmpDir = Magento_Test_Bootstrap::getInstance()->getTmpDir() . '/publication';
    }

    protected function setUp()
    {
        Mage::app()->getConfig()->getOptions()->setDesignDir(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'design'
        );

        $this->_model = Mage::getModel('Mage_Core_Model_Design_Package');
        $this->_model->setDesignTheme('test/default', 'frontend');
    }

    protected function tearDown()
    {
        Varien_Io_File::rmdirRecursive(self::$_skinPublicDir);
        Varien_Io_File::rmdirRecursive(self::$_fixtureTmpDir);
        $this->_model = null;
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetPublicSkinDir()
    {
        Mage::app()->getConfig()->getOptions()->setMediaDir(__DIR__);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . 'theme', $this->_model->getPublicDir());
    }

    /**
     * Test that URL for a skin file meets expectations
     *
     * @param string $file
     * @param string $expectedUrl
     * @param string|null $locale
     */
    protected function _testGetSkinUrl($file, $expectedUrl, $locale = null)
    {
        Mage::app()->getLocale()->setLocale($locale);
        $url = $this->_model->getViewFileUrl($file);
        $this->assertStringEndsWith($expectedUrl, $url);
        $skinFile = $this->_model->getViewFile($file);
        $this->assertFileExists($skinFile);
    }

    /**
     * @magentoConfigFixture default/design/theme/allow_view_files_duplication 1
     * @dataProvider getSkinUrlFilesDuplicationDataProvider
     */
    public function testGetSkinUrlFilesDuplication($file, $expectedUrl, $locale = null)
    {
        $this->_testGetSkinUrl($file, $expectedUrl, $locale);
    }

    /**
     * @return array
     */
    public function getSkinUrlFilesDuplicationDataProvider()
    {
        return array(
            'theme file' => array(
                'css/styles.css',
                'theme/frontend/test/default/en_US/css/styles.css',
            ),
            'theme localized file' => array(
                'logo.gif',
                'theme/frontend/test/default/fr_FR/logo.gif',
                'fr_FR',
            ),
            'modular file' => array(
                'Module::favicon.ico',
                'theme/frontend/test/default/en_US/Module/favicon.ico',
            ),
            'lib file' => array(
                'varien/product.js',
                'http://localhost/pub/lib/varien/product.js',
            ),
            'lib folder' => array(
                'varien',
                'http://localhost/pub/lib/varien',
            )
        );
    }

    /**
     * @magentoConfigFixture default/design/theme/allow_view_files_duplication 0
     * @dataProvider testGetSkinUrlNoFilesDuplicationDataProvider
     */
    public function testGetSkinUrlNoFilesDuplication($file, $expectedUrl, $locale = null)
    {
        $this->_testGetSkinUrl($file, $expectedUrl, $locale);
    }

    /**
     * @return array
     */
    public function testGetSkinUrlNoFilesDuplicationDataProvider()
    {
        return array(
            'theme css file' => array(
                'css/styles.css',
                'theme/frontend/test/default/en_US/css/styles.css',
            ),
            'theme file' => array(
                'images/logo.gif',
                'theme/frontend/test/default/images/logo.gif',
            ),
            'theme localized file' => array(
                'logo.gif',
                'theme/frontend/test/default/locale/fr_FR/logo.gif',
                'fr_FR',
            )
        );
    }

    /**
     * @magentoConfigFixture default/design/theme/allow_view_files_duplication 0
     */
    public function testGetSkinUrlNoFilesDuplicationWithCaching()
    {
        Mage::app()->getLocale()->setLocale('en_US');
        $skinParams = array('package' => 'test', 'theme' => 'default', 'skin' => 'default');
        $cacheKey = 'frontend/test/default/en_US';
        Mage::app()->cleanCache();

        $skinFile = 'images/logo.gif';
        $this->_model->getViewFileUrl($skinFile, $skinParams);
        $map = unserialize(Mage::app()->loadCache($cacheKey));
        $this->assertTrue(count($map) == 1);
        $this->assertStringEndsWith('logo.gif', (string)array_pop($map));

        $skinFile = 'images/logo_email.gif';
        $this->_model->getViewFileUrl($skinFile, $skinParams);
        $map = unserialize(Mage::app()->loadCache($cacheKey));
        $this->assertTrue(count($map) == 2);
        $this->assertStringEndsWith('logo_email.gif', (string)array_pop($map));
    }

    /**
     * @param string $file
     * @expectedException Magento_Exception
     * @dataProvider getSkinUrlDataExceptionProvider
     */
    public function testGetSkinUrlException($file)
    {
        $this->_model->getViewFileUrl($file);
    }

    /**
     * @return array
     */
    public function getSkinUrlDataExceptionProvider()
    {
        return array(
            'non-existing theme file'  => array('path/to/non-existing-file.ext'),
            'non-existing module file' => array('Some_Module::path/to/non-existing-file.ext'),
        );
    }

    /**
     * Publication of skin files in development mode
     *
     * @param string $file
     * @param $designParams
     * @param string $expectedFile
     * @dataProvider publishSkinFileDataProvider
     */
    public function testPublishSkinFile($file, $designParams, $expectedFile)
    {
        $expectedFile = self::$_skinPublicDir . '/' . $expectedFile;

        // test doesn't make sense if the original file doesn't exist or the target file already exists
        $originalFile = $this->_model->getViewFile($file, $designParams);
        $this->assertFileExists($originalFile);

        // getSkinUrl() will trigger publication in development mode
        $this->assertFileNotExists($expectedFile, 'Please verify isolation from previous test(s).');
        $this->_model->getViewFileUrl($file, $designParams);
        $this->assertFileExists($expectedFile);

        // as soon as the files are published, they must have the same mtime as originals
        $this->assertEquals(filemtime($originalFile), filemtime($expectedFile),
            "These files mtime must be equal: {$originalFile} / {$expectedFile}"
        );
    }

    /**
     * @return array
     */
    public function publishSkinFileDataProvider()
    {
        $designParams = array(
            'area'    => 'frontend',
            'package' => 'test',
            'theme'   => 'default',
        );
        return array(
            'skin file' => array(
                'images/logo_email.gif',
                $designParams,
                'frontend/test/default/en_US/images/logo_email.gif',
            ),
            'skin modular file' => array(
                'Mage_Page::favicon.ico',
                $designParams,
                'frontend/test/default/en_US/Mage_Page/favicon.ico',
            ),
        );
    }

    /**
     * Publication of CSS files located in the theme (development mode)
     */
    public function testPublishCssFileFromTheme()
    {
        $expectedFiles = array(
            'css/file.css',
            'recursive.css',
            'recursive.gif',
            'css/deep/recursive.css',
            'recursive2.gif',
            'css/body.gif',
            'css/1.gif',
            'h1.gif',
            'images/h2.gif',
            'Namespace_Module/absolute_valid_module.gif',
            'Mage_Page/favicon.ico', // non-fixture file from real module
        );
        $publishedDir = self::$_skinPublicDir . '/frontend/package/default/en_US';
        $this->assertFileNotExists($publishedDir, 'Please verify isolation from previous test(s).');
        $this->_model->getViewFileUrl('css/file.css', array('package' => 'package'));
        foreach ($expectedFiles as $file) {
            $this->assertFileExists("{$publishedDir}/{$file}");
        }
        $this->assertFileNotExists("{$publishedDir}/absolute.gif");
    }

    /**
     * Publication of CSS files located in the module
     * @dataProvider publishCssFileFromModuleDataProvider
     */
    public function testPublishCssFileFromModule(
        $cssSkinFile, $designParams, $expectedCssFile, $expectedCssContent, $expectedRelatedFiles
    ) {
        $this->_model->getViewFileUrl($cssSkinFile, $designParams);

        $expectedCssFile = self::$_skinPublicDir . '/' . $expectedCssFile;
        $this->assertFileExists($expectedCssFile);
        $actualCssContent = file_get_contents($expectedCssFile);

        $this->assertNotRegExp(
            '/url\(.*?' . Mage_Core_Model_Design_Package::SCOPE_SEPARATOR . '.*?\)/',
            $actualCssContent,
            'Published CSS file must not contain scope separators in URLs.'
        );

        foreach ($expectedCssContent as $expectedCssSubstring) {
            $this->assertContains($expectedCssSubstring, $actualCssContent);
        }

        foreach ($expectedRelatedFiles as $expectedFile) {
            $expectedFile = self::$_skinPublicDir . '/' . $expectedFile;
            $this->assertFileExists($expectedFile);
        }
    }

    public function publishCssFileFromModuleDataProvider()
    {
        return array(
            'frontend' => array(
                'widgets.css',
                array(
                    'area'    => 'frontend',
                    'package' => 'default',
                    'theme'   => 'demo',
                    'module'  => 'Mage_Reports',
                ),
                'frontend/default/demo/en_US/Mage_Reports/widgets.css',
                array(
                    'url(../Mage_Catalog/images/i_block-list.gif)',
                ),
                array(
                    'frontend/default/demo/en_US/Mage_Catalog/images/i_block-list.gif',
                ),
            ),
            'adminhtml' => array(
                'Mage_Paypal::boxes.css',
                array(
                    'area'    => 'adminhtml',
                    'package' => 'package',
                    'theme'   => 'test',
                    'module'  => false,
                ),
                'adminhtml/package/test/en_US/Mage_Paypal/boxes.css',
                array(
                    'url(logo.gif)',
                    'url(section.png)',
                ),
                array(
                    'adminhtml/package/test/en_US/Mage_Paypal/logo.gif',
                    'adminhtml/package/test/en_US/Mage_Paypal/section.png',
                ),
            ),
        );
    }


    /**
     * Test that modified CSS file and changed resources are re-published in developer mode
     */
    public function testPublishResourcesAndCssWhenChangedCssDevMode()
    {
        if (!Mage::getIsDeveloperMode()) {
            $this->markTestSkipped('Valid in developer mode only');
        }
        $this->_testPublishResourcesAndCssWhenChangedCss(true);
    }

    /**
     * Test that modified CSS file and changed resources are not re-published in usual mode
     */
    public function testNotPublishResourcesAndCssWhenChangedCssUsualMode()
    {
        if (Mage::getIsDeveloperMode()) {
            $this->markTestSkipped('Valid in non-developer mode only');
        }
        $this->_testPublishResourcesAndCssWhenChangedCss(false);
    }

    /**
     * Tests what happens when CSS file and its resources are changed - whether they are re-published or not
     *
     * @param bool $expectedPublished
     */
    protected function _testPublishResourcesAndCssWhenChangedCss($expectedPublished)
    {
        $fixtureSkinPath = self::$_fixtureTmpDir . '/frontend/test/default/';
        $publishedPath = self::$_skinPublicDir . '/frontend/test/default/en_US/';

        // Prepare temporary fixture directory and publish files from it
        $this->_copyFixtureSkinToTmpDir($fixtureSkinPath);
        $this->_model->getViewFileUrl('style.css');

        // Change main file and referenced files - everything changed and referenced must appear
        file_put_contents(
            $fixtureSkinPath . 'style.css',
            'div {background: url(images/rectangle.gif);}',
            FILE_APPEND
        );
        file_put_contents(
            $fixtureSkinPath . 'sub.css',
            '.sub2 {border: 1px solid magenta}',
            FILE_APPEND
        );
        $this->_model->getViewFileUrl('style.css');

        $assertFileComparison = $expectedPublished ? 'assertFileEquals' : 'assertFileNotEquals';
        $this->$assertFileComparison($fixtureSkinPath . 'style.css', $publishedPath . 'style.css');
        $this->$assertFileComparison($fixtureSkinPath . 'sub.css', $publishedPath . 'sub.css');
        if ($expectedPublished) {
            $this->assertFileEquals(
                $fixtureSkinPath . 'images/rectangle.gif', $publishedPath . 'images/rectangle.gif'
            );
        } else {
            $this->assertFileNotExists($publishedPath . 'images/rectangle.gif');
        }
    }


    /**
     * Test changed resources, referenced in non-modified CSS file, are re-published
     * @magentoAppIsolation enabled
     */
    public function testPublishChangedResourcesWhenUnchangedCssDevMode()
    {
        if (!Mage::getIsDeveloperMode()) {
            $this->markTestSkipped('Valid in developer mode only');
        }

        $this->_testPublishChangedResourcesWhenUnchangedCss(true);
    }

    /**
     * Test changed resources, referenced in non-modified CSS file, are re-published
     * @magentoAppIsolation enabled
     */
    public function testNotPublishChangedResourcesWhenUnchangedCssUsualMode()
    {
        if (Mage::getIsDeveloperMode()) {
            $this->markTestSkipped('Valid in non-developer mode only');
        }

        $this->_testPublishChangedResourcesWhenUnchangedCss(false);
    }

    /**
     * Tests what happens when CSS file and its resources are changed - whether they are re-published or not
     *
     * @param bool $expectedPublished
     */
    protected function _testPublishChangedResourcesWhenUnchangedCss($expectedPublished)
    {
        $fixtureSkinPath = self::$_fixtureTmpDir . '/frontend/test/default/';
        $publishedPath = self::$_skinPublicDir . '/frontend/test/default/en_US/';

        // Prepare temporary fixture directory and publish files from it
        $this->_copyFixtureSkinToTmpDir($fixtureSkinPath);
        $this->_model->getViewFileUrl('style.css');

        // Change referenced files
        copy($fixtureSkinPath . 'images/rectangle.gif', $fixtureSkinPath . 'images/square.gif');
        touch($fixtureSkinPath . 'images/square.gif');
        file_put_contents(
            $fixtureSkinPath . 'sub.css',
            '.sub2 {border: 1px solid magenta}',
            FILE_APPEND
        );

        $this->_model->getViewFileUrl('style.css');

        $assertFileComparison = $expectedPublished ? 'assertFileEquals' : 'assertFileNotEquals';
        $this->$assertFileComparison($fixtureSkinPath . 'sub.css', $publishedPath . 'sub.css');
        $this->$assertFileComparison($fixtureSkinPath . 'images/rectangle.gif', $publishedPath . 'images/square.gif');
    }

    /**
     * Prepare design directory with initial css and resources
     *
     * @param string $fixtureSkinPath
     */
    protected function _copyFixtureSkinToTmpDir($fixtureSkinPath)
    {
        Mage::app()->getConfig()->getOptions()->setDesignDir(self::$_fixtureTmpDir);
        mkdir($fixtureSkinPath . '/images', 0777, true);

        // Copy all files to fixture location
        $mTime = time() - 10; // To ensure that all files, changed later in test, will be recognized for publication
        $sourcePath = dirname(__DIR__) . '/_files/design/frontend/test/publication/';
        $files = array('theme.xml', 'style.css', 'sub.css', 'images/square.gif', 'images/rectangle.gif');
        foreach ($files as $file) {
            copy($sourcePath . $file, $fixtureSkinPath . $file);
            touch($fixtureSkinPath . $file, $mTime);
        }
    }
}
