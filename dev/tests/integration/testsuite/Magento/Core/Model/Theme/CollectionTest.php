<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for filesystem themes collection
 */
namespace Magento\Core\Model\Theme;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Theme\Collection
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Theme\Collection');
        $this->_model->setBaseDir(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files'. DIRECTORY_SEPARATOR . 'design');
    }

    /**
     * Test load themes collection from filesystem
     *
     * @magentoAppIsolation enabled
     */
    public function testLoadThemesFromFileSystem()
    {
        $pathPattern = implode(DS, array('frontend', '*', 'theme.xml'));
        $this->_model->addTargetPattern($pathPattern);
        $this->assertEquals(8, count($this->_model));
    }

    /**
     * Load from configuration
     *
     * @dataProvider expectedThemeDataFromConfiguration
     */
    public function testLoadFromConfiguration($themePath, $expectedData)
    {
        $theme = $this->_model->addTargetPattern($themePath)->getFirstItem();
        $this->assertEquals($expectedData, $theme->getData());
    }

    /**
     * Expected theme data from configuration
     *
     * @return array
     */
    public function expectedThemeDataFromConfiguration()
    {
        return array(
            array(
                'themePath'    => implode(DIRECTORY_SEPARATOR, array('frontend', 'magento_default', 'theme.xml')),
                'expectedData' => array(
                    'area'                 => 'frontend',
                    'theme_title'          => 'Default',
                    'theme_version'        => '2.0.0.0',
                    'parent_id'            => null,
                    'parent_theme_path'    => null,
                    'theme_path'           => 'magento_default',
                    'code'                 => 'magento_default',
                    'preview_image'        => null,
                    'type'                 => \Magento\Core\Model\Theme::TYPE_PHYSICAL
                )
            )
        );
    }

    /**
     * Test is theme present in file system
     *
     * @magentoAppIsolation enabled
     * @covers \Magento\Core\Model\Theme\Collection::hasTheme
     */
    public function testHasThemeInCollection()
    {
        /** @var $themeModel \Magento\View\Design\Theme */
        $themeModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\View\Design\Theme');
        $themeModel->setData(array(
            'area'                 => 'space_area',
            'theme_title'          => 'Space theme',
            'theme_version'        => '2.0.0.0',
            'parent_id'            => null,
            'is_featured'          => false,
            'theme_path'           => 'default_space',
            'preview_image'        => 'images/preview.png',
            'type'                 => \Magento\Core\Model\Theme::TYPE_PHYSICAL
        ));

        $this->_model->addDefaultPattern('*');
        $this->assertFalse($this->_model->hasTheme($themeModel));
    }
}
