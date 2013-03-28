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

class Mage_Core_Model_Design_FileResolution_Strategy_FallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_baseDir;

    /**
     * @var string
     */
    protected $_viewDir;

    public function setUp()
    {
        $this->_baseDir = realpath(__DIR__ . '/../../../_files/fallback');
        $this->_viewDir = $this->_baseDir . DIRECTORY_SEPARATOR . 'design';
    }

    /**
     * Build a model to test
     *
     * @return Mage_Core_Model_Design_FileResolution_Strategy_Fallback
     */
    protected function _buildModel()
    {
        // Prepare config with directories
        $dirs = new Mage_Core_Model_Dir(
            Mage::getObjectManager()->get('Magento_Filesystem'),
            $this->_baseDir,
            array(),
            array(Mage_Core_Model_Dir::THEMES => $this->_viewDir)
        );

        $fallbackFile = new Mage_Core_Model_Design_Fallback_List_File($dirs);
        $fallbackLocale = new Mage_Core_Model_Design_Fallback_List_Locale($dirs);
        $fallbackViewFile = new Mage_Core_Model_Design_Fallback_List_View($dirs);

        return Mage::getObjectManager()->create(
            'Mage_Core_Model_Design_FileResolution_Strategy_Fallback',
            array('dirs' => $dirs, 'fallbackFile' => $fallbackFile, 'fallbackLocale' => $fallbackLocale,
                'fallbackViewFile' => $fallbackViewFile
            )
        );
    }

    /**
     * Compose custom theme model with designated path
     *
     * @param string $area
     * @param string $themePath
     * @return Mage_Core_Model_Theme
     */
    protected function _getThemeModel($area, $themePath)
    {
        /** @var $collection Mage_Core_Model_Theme_Collection */
        $collection = Mage::getModel('Mage_Core_Model_Theme_Collection');
        $themeModel = $collection->setBaseDir($this->_viewDir)
            ->addDefaultPattern()
            ->addFilter('theme_path', $themePath)
            ->addFilter('area', $area)
            ->getFirstItem();
        return $themeModel;
    }

    /**
     * @param string $file
     * @param string $area
     * @param string $themePath
     * @param string|null $module
     * @param string|null $expectedFilename
     *
     * @dataProvider getFileDataProvider
     */
    public function testGetFile($file, $area, $themePath, $module, $expectedFilename)
    {
        $model = $this->_buildModel($area, $themePath, null);
        $themeModel = $this->_getThemeModel($area, $themePath);

        $expectedFilename = str_replace('/', DS, $expectedFilename);
        $actualFilename = $model->getFile($area, $themeModel, $file, $module);
        if ($expectedFilename) {
            $this->assertStringMatchesFormat($expectedFilename, $actualFilename);
            $this->assertFileExists($actualFilename);
        } else {
            $this->assertFileNotExists($actualFilename);
        }
    }

    /**
     * @return array
     */
    public function getFileDataProvider()
    {
        return array(
            'no default theme inheritance' => array(
                'fixture_template.phtml', 'frontend', 'package/standalone_theme', null, null
            ),
            'same package & parent theme' => array(
                'fixture_template_two.phtml', 'frontend', 'package/custom_theme3', null,
                "%s/frontend/package/custom_theme/fixture_template_two.phtml",
            ),
            'same package & grandparent theme' => array(
                'fixture_template.phtml', 'frontend', 'package/custom_theme3', null,
                "%s/frontend/package/default/fixture_template.phtml",
            ),
            'parent package & parent theme' => array(
                'fixture_template_two.phtml', 'frontend', 'test/external_package_descendant', null,
                "%s/frontend/package/custom_theme/fixture_template_two.phtml",
            ),
            'parent package & grandparent theme' => array(
                'fixture_template.phtml', 'frontend', 'test/external_package_descendant', null,
                "%s/frontend/package/default/fixture_template.phtml",
            ),
            'module file inherited by scheme' => array(
                'theme_template.phtml', 'frontend', 'test/test_theme', 'Mage_Catalog',
                "%s/frontend/test/default/Mage_Catalog/theme_template.phtml",
            )
        );
    }

    /**
     * @param string $file
     * @param string $area
     * @param string $themePath
     * @param string $locale
     * @param string|null $expectedFilename
     *
     * @dataProvider getLocaleFileDataProvider
     */
    public function testLocaleFileFallback($file, $area, $themePath, $locale, $expectedFilename)
    {
        $model = $this->_buildModel($area, $themePath, $locale);
        $themeModel = $this->_getThemeModel($area, $themePath);

        $expectedFilename = str_replace('/', DIRECTORY_SEPARATOR, $expectedFilename);
        $actualFilename = $model->getLocaleFile($area, $themeModel, $locale, $file);
        if ($expectedFilename) {
            $this->assertStringMatchesFormat($expectedFilename, $actualFilename);
            $this->assertFileExists($actualFilename);
        } else {
            $this->assertFileNotExists($actualFilename);
        }
    }

    /**
     * @return array
     */
    public function getLocaleFileDataProvider()
    {
        return array(
            'no default theme inheritance' => array(
                'fixture_translate.csv', 'frontend', 'package/standalone_theme', 'en_US', null
            ),
            'parent theme' => array(
                'fixture_translate_two.csv', 'frontend', 'package/custom_theme3', 'en_US',
                "%s/frontend/package/custom_theme/locale/en_US/fixture_translate_two.csv",
            ),
            'grandparent theme' => array(
                'fixture_translate.csv', 'frontend', 'package/custom_theme3', 'en_US',
                "%s/frontend/package/default/locale/en_US/fixture_translate.csv",
            ),
        );
    }

    /**
     * Test for the skin files fallback
     *
     * @param string $file
     * @param string $area
     * @param string $themePath
     * @param string|null $locale
     * @param string|null $module
     * @param string|null $expectedFilename
     */
    protected function _testGetSkinFile($file, $area, $themePath, $locale, $module, $expectedFilename)
    {
        $model = $this->_buildModel($area, $themePath, $locale);
        $themeModel = $this->_getThemeModel($area, $themePath);

        $expectedFilename = str_replace('/', DIRECTORY_SEPARATOR, $expectedFilename);
        $actualFilename = $model->getViewFile($area, $themeModel, $locale, $file, $module);
        if ($expectedFilename) {
            $this->assertStringMatchesFormat($expectedFilename, $actualFilename);
            $this->assertFileExists($actualFilename);
        } else {
            $this->assertFileNotExists($actualFilename);
        }
    }

    /**
     * Test for the skin files fallback according to the themes inheritance
     *
     * @param string $file
     * @param string $area
     * @param string $themePath
     * @param string $locale
     * @param string|null $expectedFilename
     *
     * @dataProvider getSkinFileThemeDataProvider
     */
    public function testGetSkinFileTheme($file, $area, $themePath, $locale, $expectedFilename)
    {
        $this->_testGetSkinFile($file, $area, $themePath, $locale, null, $expectedFilename);
    }

    /**
     * @return array
     */
    public function getSkinFileThemeDataProvider()
    {
        return array(
            'no default theme inheritance' => array(
                'fixture_script_two.js', 'frontend', 'package/standalone_theme', 'en_US',
                null,
            ),
            'same theme & default skin' => array(
                'fixture_script_two.js', 'frontend', 'package/custom_theme', 'en_US',
                "%s/frontend/package/custom_theme/fixture_script_two.js",
            ),
            'parent theme & same skin' => array(
                'fixture_script.js', 'frontend', 'package/custom_theme3', 'en_US',
                "%s/frontend/package/custom_theme2/fixture_script.js",
            ),
            'parent theme & default skin' => array(
                'fixture_script_two.js', 'frontend', 'package/custom_theme3', 'en_US',
                "%s/frontend/package/custom_theme/fixture_script_two.js",
            ),
            'grandparent theme & same skin' => array(
                'fixture_script_three.js', 'frontend', 'package/custom_theme3',
                'en_US',  null,
            ),
            'grandparent theme & default skin' => array(
                'fixture_script_four.js', 'frontend', 'package/custom_theme3',
                'en_US', "%s/frontend/package/default/fixture_script_four.js",
            ),
            'parent package & same theme & same skin' => array(
                'fixture_script.js', 'frontend/test', 'external_package_descendant', 'en_US',
                null,
            ),
            'parent package & same theme & default skin' => array(
                'fixture_script_two.js', 'frontend', 'test/external_package_descendant',
                'en_US', "%s/frontend/package/custom_theme/fixture_script_two.js",
            ),
        );
    }

    /**
     * Test for the skin files localization
     *
     * @param string $file
     * @param string $area
     * @param string $themePath
     * @param string $locale
     * @param string|null $module
     * @param string|null $expectedFilename
     *
     * @dataProvider getSkinFileL10nDataProvider
     */
    public function testGetSkinFileL10n($file, $area, $themePath, $locale, $module, $expectedFilename)
    {
        $this->_testGetSkinFile($file, $area, $themePath, $locale, $module, $expectedFilename);
    }

    /**
     * @return array
     */
    public function getSkinFileL10nDataProvider()
    {
        return array(
            'general skin file' => array(
                'fixture_script.js', 'frontend', 'package/custom_theme2', 'en_US', null,
                "%s/frontend/package/custom_theme2/fixture_script.js"
            ),
            'localized skin file' => array(
                'fixture_script.js', 'frontend', 'package/custom_theme2', 'ru_RU', null,
                "%s/frontend/package/custom_theme2/locale/ru_RU/fixture_script.js",
            ),
            'general modular skin file' => array(
                'fixture_script.js', 'frontend', 'package/custom_theme2', 'en_US',
                'Fixture_Module',
                "%s/frontend/package/custom_theme2/fixture_script.js",
            ),
            'localized modular skin file' => array(
                'fixture_script.js', 'frontend', 'package/custom_theme2', 'ru_RU',
                'Fixture_Module',
                "%s/frontend/package/custom_theme2/locale/ru_RU/fixture_script.js",
            ),
        );
    }

    /**
     * Test for the skin files fallback to the JavaScript libraries
     *
     * @param string $file
     * @param string $area
     * @param string $themePath
     * @param string|null $expectedFilename
     *
     * @dataProvider getSkinFileJsLibDataProvider
     */
    public function testGetSkinFileJsLib($file, $area, $themePath, $expectedFilename)
    {
        $this->_testGetSkinFile($file, $area, $themePath, 'en_US', null, $expectedFilename);
    }

    /**
     * @return array
     */
    public function getSkinFileJsLibDataProvider()
    {
        return array(
            'lib file in theme' => array(
                'mage/script.js', 'frontend', 'package/custom_theme2',
                "%s/frontend/package/custom_theme2/mage/script.js",
            ),
            'lib file in js lib' => array(
                'mage/script.js', 'frontend', 'package/custom_theme',
                '%s/pub/lib/mage/script.js',
            ),
        );
    }
}
