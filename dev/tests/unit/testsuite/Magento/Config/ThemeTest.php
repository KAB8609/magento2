<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Config_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructException()
    {
        new Magento_Config_Theme(array());
    }

    public function testGetSchemaFile()
    {
        $config = new Magento_Config_Theme(array(
            sprintf('%s/_files/packages/%s/theme.xml', __DIR__, 'default/default')
        ));

        $this->assertFileExists($config->getSchemaFile());
    }

    /**
     * @param string $themePath
     * @param mixed $expected
     * @dataProvider getThemeTitleDataProvider
     */
    public function testGetThemeTitle($themePath, $expected)
    {
        $config = new Magento_Config_Theme(array(
            sprintf('%s/_files/packages/%s/theme.xml', __DIR__, $themePath)
        ));
        $this->assertSame($expected, $config->getThemeTitle());
    }

    /**
     * @return array
     */
    public function getThemeTitleDataProvider()
    {
        return array(
            array('default_default', 'Default'),
            array('default_test',    'Test'),
        );
    }

    /**
     * @param string $themePath
     * @param mixed $expected
     * @dataProvider getParentThemeDataProvider
     */
    public function testGetParentTheme($themePath, $expected)
    {
        $config = new Magento_Config_Theme(array(
            sprintf('%s/_files/packages/%s/theme.xml', __DIR__, $themePath)
        ));
        $this->assertSame($expected, $config->getParentTheme());
    }

    /**
     * @return array
     */
    public function getParentThemeDataProvider()
    {
        return array(
            array('default_default', null),
            array('default_test', array('default')),
            array('default_test2', array('test')),
            array('test_external_package_descendant', array('test2')),
        );
    }
}
