<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Magento\Widget;

class TemplateFilesTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('frontend');
    }

    /**
     * Check if all the declared widget templates actually exist
     *
     * @param string $class
     * @param string $template
     * @dataProvider widgetTemplatesDataProvider
     */
    public function testWidgetTemplates($class, $template)
    {
        /** @var $blockFactory \Magento\View\Element\BlockFactory */
        $blockFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\Element\BlockFactory');
        /** @var \Magento\View\Element\Template $block */
        $block = $blockFactory->createBlock($class);
        $this->assertInstanceOf('Magento\View\Block\Template', $block);
        $block->setTemplate((string)$template);
        $this->assertFileExists($block->getTemplateFile());
    }

    /**
     * Collect all declared widget blocks and templates
     *
     * @return array
     */
    public function widgetTemplatesDataProvider()
    {
        $result = array();
        /** @var $model \Magento\Widget\Model\Widget */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Widget\Model\Widget');
        foreach ($model->getWidgetsArray() as $row) {
            /** @var $instance \Magento\Widget\Model\Widget\Instance */
            $instance = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Widget\Model\Widget\Instance');
            $config = $instance->setType($row['type'])->getWidgetConfigAsArray();
            $class = $row['type'];
            if (is_subclass_of($class, 'Magento\View\Element\Template')) {
                if (isset($config['parameters']) && isset($config['parameters']['template'])
                    && isset($config['parameters']['template']['values'])) {
                    $templates = $config['parameters']['template']['values'];
                    foreach ($templates as $template) {
                        if (isset($template['value'])) {
                            $result[] = array($class, (string)$template['value']);
                        }
                    }
                }
            }
        }
        return $result;
    }
}
