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
 * Test theme copy functionality
 */
class Mage_Core_Model_Theme_Copy_VirtualToStagingTest extends Mage_Core_Model_Theme_Copy_TestCase
{
    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Theme_Copy_VirtualToStaging
     */
    protected function _getCopyModel()
    {
        $constructorArgs = array(
            $this->_getThemeFactory(),
            $this->_getLayoutLink(),
            $this->_getLayoutUpdate(),
            array()
        );
        return $this->getMockBuilder('Mage_Core_Model_Theme_Copy_VirtualToStaging')
            ->setMethods(null)
            ->setConstructorArgs($constructorArgs)
            ->getMock();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Mage/Core/_files/layout_update.php
     */
    public function testCopyVirtualToStaging()
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_collection->getThemeByFullPath('frontend/test/test');
        $this->assertNotEmpty($theme, 'Test theme not found');

        $stagingTheme = $this->_model->copy($theme);
        $this->assertNotEmpty($stagingTheme, 'Staging theme was not created');
        $this->assertSame($theme->getId(), $stagingTheme->getParentId());

        $linkCollection = $this->_getLayoutLink()->getCollection();
        $linkCollection->addFieldToFilter('theme_id', $stagingTheme->getId());
        $this->assertGreaterThan(0, $linkCollection->getSize());
    }
}
