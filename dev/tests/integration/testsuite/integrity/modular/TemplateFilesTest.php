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

require_once __DIR__ . '/../../../../static/testsuite/Utility/Classes.php';

/**
 * @group integrity
 */
class Integrity_Modular_TemplateFilesTest extends PHPUnit_Framework_TestCase // Magento_Test_TestCase_IntegrityAbstract
{
    /**
     * @param string $module
     * @param string $template
     * @param string $class
     * @param string $area
     * @dataProvider allTemplatesDataProvider
     */
    public function testAllTemplates($module, $template, $class, $area)
    {
        $params = array(
            '_area'    => $area,
            '_package' => 'nonexisting_package', // intentionally to make sure the module files will be requested
            '_theme'   => 'nonexisting_theme',
            '_module'  => $module
        );
        $file = Mage::getDesign()->getTemplateFilename($template, $params);
        $this->assertFileExists($file, "Block class: {$class}");
    }

    /**
     * @return array
     */
    public function allTemplatesDataProvider()
    {
        $templates = array();
        foreach (Utility_Classes::collectModuleClasses('Block') as $blockClass => $module) {
            $class = new ReflectionClass($blockClass);
            if ($class->isAbstract() || !$class->isSubclassOf('Mage_Core_Block_Template')) {
                continue;
            }
            $block = new $blockClass;
            $template = $block->getTemplate();
            if ($template) {
                $area = 'frontend';
                if ($module == 'Mage_Install') {
                    $area = 'install';
                } elseif ($module == 'Mage_Adminhtml' || strpos($blockClass, '_Adminhtml_')
                    || ($block instanceof Mage_Adminhtml_Block_Template)
                ) {
                    $area = 'adminhtml';
                }
                $templates[] = array($module, $template, $blockClass, $area);
            }
        }
        return $templates;
    }
}
