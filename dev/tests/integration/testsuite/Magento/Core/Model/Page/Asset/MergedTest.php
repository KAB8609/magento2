<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
 */
class Magento_Core_Model_Page_Asset_MergedTest extends PHPUnit_Framework_TestCase
{
    /**
     * Path to the public directory for view files
     *
     * @var string
     */
    protected static $_themePublicDir;

    /**
     * Path to the public directory for merged view files
     *
     * @var string
     */
    protected static $_viewPublicMergedDir;

    public static function setUpBeforeClass()
    {
        /** @var $service Magento_Core_Model_View_Service */
        $service = Mage::getObjectManager()->get('Magento_Core_Model_View_Service');
        self::$_themePublicDir = $service->getPublicDir();

        /** @var Magento_Core_Model_Dir $dirs */
        $dirs = Mage::getObjectManager()->get('Magento_Core_Model_Dir');
        self::$_viewPublicMergedDir = $dirs->getDir(Magento_Core_Model_Dir::PUB_VIEW_CACHE)
            . DIRECTORY_SEPARATOR . Magento_Core_Model_Page_Asset_Merged::PUBLIC_MERGE_DIR;
    }

    public function setUp()
    {
        Magento_Test_Helper_Bootstrap::getInstance()->reinitialize(array(
            Mage::PARAM_APP_DIRS => array(
                Magento_Core_Model_Dir::THEMES => realpath(__DIR__ . '/../../_files/design')
            )
        ));
        Mage::getDesign()->setDesignTheme('vendor_default');
    }

    public function tearDown()
    {
        $filesystem = Mage::getObjectManager()->create('Magento_Filesystem');
        $filesystem->delete(self::$_themePublicDir . '/frontend');
        $filesystem->delete(self::$_viewPublicMergedDir);
    }

    /**
     * Build model, containing the provided assets
     *
     * @param array $files
     * @param string $contentType
     * @return Magento_Core_Model_Page_Asset_Merged
     */
    protected function _buildModel(array $files, $contentType)
    {
        $assets = array();
        foreach ($files as $file) {
            $assets[] = Mage::getModel('Magento_Core_Model_Page_Asset_ViewFile',
                array('file' => $file, 'contentType' => $contentType));
        }
        $model = Mage::getModel('Magento_Core_Model_Page_Asset_Merged', array('assets' => $assets));
        return $model;
    }

    /**
     * @param string $contentType
     * @param array $files
     * @param string $expectedFilename
     * @param array $related
     * @dataProvider getUrlDataProvider
     * @magentoConfigFixture current_store dev/css/merge_css_files 1
     * @magentoConfigFixture current_store dev/js/merge_files 1
     * @magentoConfigFixture current_store dev/static/sign 0
     */
    public function testMerging($contentType, $files, $expectedFilename, $related = array())
    {
        $resultingFile = self::$_viewPublicMergedDir . '/' . $expectedFilename;
        $this->assertFileNotExists($resultingFile);

        $model = $this->_buildModel($files, $contentType);

        $this->assertCount(1, $model);

        $model->rewind();
        $asset = $model->current();
        $mergedUrl = $asset->getUrl();
        $this->assertEquals($expectedFilename, basename($mergedUrl));

        $this->assertFileExists($resultingFile);
        foreach ($related as $file) {
            $this->assertFileExists(
                self::$_themePublicDir . '/frontend/vendor_default/en_US/' . $file
            );
        }
    }

    /**
     * @param string $contentType
     * @param array $files
     * @param string $expectedFilename
     * @param array $related
     * @dataProvider getUrlDataProvider
     * @magentoConfigFixture current_store dev/css/merge_css_files 1
     * @magentoConfigFixture current_store dev/js/merge_files 1
     * @magentoConfigFixture current_store dev/static/sign 1
     */
    public function testMergingAndSigning($contentType, $files, $expectedFilename, $related = array())
    {
        $model = $this->_buildModel($files, $contentType);

        $asset = $model->current();
        $mergedUrl = $asset->getUrl();
        $mergedFileName = basename($mergedUrl);
        $mergedFileName = preg_replace('/\?.*$/i', '', $mergedFileName);
        $this->assertEquals($expectedFilename, $mergedFileName);

        foreach ($related as $file) {
            $this->assertFileExists(
                self::$_themePublicDir . '/frontend/vendor_default/en_US/' . $file
            );
        }
    }

    /**
     * @return array
     */
    public static function getUrlDataProvider()
    {
        return array(
            array(
                Magento_Core_Model_View_Publisher::CONTENT_TYPE_CSS,
                array(
                    'mage/calendar.css',
                    'css/file.css',
                ),
                '67b062e295aeb5a09b62c86d2823632a.css',
                array(
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
                    'Magento_Page/favicon.ico', // non-fixture file from real module
                ),
            ),
            array(
                Magento_Core_Model_View_Publisher::CONTENT_TYPE_JS,
                array(
                    'mage/calendar.js',
                    'scripts.js',
                ),
                'c1a0045f608acb03f57f285c162c9f95.js',
            ),
        );
    }
}