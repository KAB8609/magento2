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

class Integrity_Theme_XmlFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider viewConfigFileDataProvider
     */
    public function testViewConfigFile($file)
    {
        if (strpos($file, 'app/design/frontend/magento2/reference/view.xml') !== false) {
            $this->markTestIncomplete('MAGETWO-9204');
        }
        $this->_validateConfigFile($file, Mage::getBaseDir('lib') . '/Magento/Config/etc/view.xsd');
    }

    /**
     * @return array
     */
    public function viewConfigFileDataProvider()
    {
        $result = array();
        foreach (glob(Mage::getBaseDir('design') . '/*/*/*/view.xml') as $file) {
            $result[$file] = array($file);
        }
        return $result;
    }

    /**
     * @param string $themeDir
     * @dataProvider themeConfigFileExistsDataProvider
     */
    public function testThemeConfigFileExists($themeDir)
    {
        $this->assertFileExists($themeDir . '/theme.xml');
    }

    /**
     * @return array
     */
    public function themeConfigFileExistsDataProvider()
    {
        $result = array();
        foreach (glob(Mage::getBaseDir('design') . '/*/*/*', GLOB_ONLYDIR) as $themeDir) {
            $result[$themeDir] = array($themeDir);
        }
        return $result;
    }

    /**
     * @param string $file
     * @dataProvider themeConfigFileDataProvider
     */
    public function testThemeConfigFileSchema($file)
    {
        $this->_validateConfigFile($file, Mage::getBaseDir('lib') . '/Magento/Config/etc/theme.xsd');
    }

    /**
     * Configuration should declare a single package/theme that corresponds to the file system directories
     *
     * @param string $file
     * @dataProvider themeConfigFileDataProvider
     */
    public function testThemeConfigFilePackageTheme($file)
    {
        list($expectedPackage, $expectedTheme) = array_slice(preg_split('[\\/]', $file), -3, 2);
        /** @var $configXml SimpleXMLElement */
        $configXml = simplexml_load_file($file);
        $actualPackages = $configXml->xpath('/design/package');
        $this->assertCount(1, $actualPackages, 'Single design package declaration is expected.');
        $this->assertEquals(
            $expectedPackage,
            $actualPackages[0]['code'],
            'Design package code does not correspond to the directory name.'
        );
        $actualThemes = $configXml->xpath('/design/package/theme');
        $this->assertCount(1, $actualThemes, 'Single theme declaration is expected.');
        $this->assertEquals(
            $expectedTheme,
            $actualThemes[0]['code'],
            'Theme code does not correspond to the directory name.'
        );
    }

    /**
     * @return array
     */
    public function themeConfigFileDataProvider()
    {
        $result = array();
        foreach (glob(Mage::getBaseDir('design') . '/*/*/*/theme.xml') as $file) {
            $result[$file] = array($file);
        }
        return $result;
    }

    /**
     * Perform test whether a configuration file is valid
     *
     * @param string $file
     * @param string $schemaFile
     * @throws PHPUnit_Framework_AssertionFailedError if file is invalid
     */
    protected function _validateConfigFile($file, $schemaFile)
    {
        $domConfig = new Magento_Config_Dom(file_get_contents($file));
        $result = $domConfig->validate($schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }
}
