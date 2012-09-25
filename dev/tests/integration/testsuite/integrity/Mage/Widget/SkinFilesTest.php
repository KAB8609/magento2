<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group integrity
 */
class Integrity_Mage_Widget_SkinFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * dataProvider widgetPlaceholderImagesDataProvider
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @todo Remove suppress warnings after the test fix
     */
    public function testWidgetPlaceholderImages(/*$skinImage*/)
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $this->assertFileExists(Mage::getDesign()->getSkinFile($skinImage, array('area' => 'adminhtml')));
    }

    /**
     * @return array
     */
    public function widgetPlaceholderImagesDataProvider()
    {
        $result = array();
        $model = new Mage_Widget_Model_Widget;
        foreach ($model->getWidgetsArray() as $row) {
            $instance = new Mage_Widget_Model_Widget_Instance;
            $config = $instance->setType($row['type'])->getWidgetConfig();
            // @codingStandardsIgnoreStart
            if (isset($config->placeholder_image)) {
                $result[] = array((string)$config->placeholder_image);
            }
            // @codingStandardsIgnoreEnd
        }
        return $result;
    }
}
