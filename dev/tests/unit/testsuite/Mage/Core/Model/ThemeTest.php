<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme model
 */
class Mage_Core_Model_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Return Mock of Theme Model loaded from configuration
     *
     * @param string $designDir
     * @param string $targetPath
     * @return mixed
     */
    protected function _getThemeModel($designDir, $targetPath)
    {
        Mage::getConfig()->getOptions()->setData('design_dir', $designDir);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        /** @var $themeMock Mage_Core_Model_Theme */
        $arguments = $objectManagerHelper->getConstructArguments(Magento_Test_Helper_ObjectManager::MODEL_ENTITY);
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('_init'), $arguments, '', true);

        /** @var $collectionMock Mage_Core_Model_Theme_Collection|PHPUnit_Framework_MockObject_MockObject */
        $collectionMock = $this->getMock('Mage_Core_Model_Theme_Collection', array('getNewEmptyItem'));
        $collectionMock->expects($this->any())
            ->method('getNewEmptyItem')
            ->will($this->returnValue($themeMock));

        return $collectionMock->setBaseDir($designDir)->addTargetPattern($targetPath)->getFirstItem();
    }

    /**
     * Test load from configuration
     *
     * @covers Mage_Core_Model_Theme::loadFromConfiguration
     */
    public function testLoadFromConfiguration()
    {
        $targetPath = implode(DS, array('frontend', 'default', 'iphone', 'theme.xml'));
        $designDir = implode(DS, array(__DIR__, '_files'));

        $this->assertEquals(
            $this->_expectedThemeDataFromConfiguration(),
            $this->_getThemeModel($designDir, $targetPath)->getData()
        );
    }

    /**
     * Test load invalid configuration
     *
     * @covers Mage_Core_Model_Theme::loadFromConfiguration
     * @expectedException Magento_Exception
     */
    public function testLoadInvalidConfiguration()
    {
        $targetPath = implode(DS, array('frontend', 'default', 'iphone', 'theme_invalid.xml'));
        $designDir = implode(DS, array(__DIR__, '_files'));

        $this->assertEquals(
            $this->_expectedThemeDataFromConfiguration(),
            $this->_getThemeModel($designDir, $targetPath)->getData()
        );
    }

    /**
     * Expected theme data from configuration
     *
     * @return array
     */
    public function _expectedThemeDataFromConfiguration()
    {
        return array(
            'parent_id'            => null,
            'theme_path'           => 'default/iphone',
            'theme_version'        => '2.0.0.1',
            'theme_title'          => 'Iphone',
            'preview_image'        => 'images/preview.png',
            'magento_version_from' => '2.0.0.1-dev1',
            'magento_version_to'   => '*',
            'is_featured'          => true,
            'theme_directory'      => implode(DS, array(__DIR__, '_files', 'frontend', 'default', 'iphone')),
            'parent_theme_path'    => null,
            'area'                 => 'frontend',
        );
    }
}
