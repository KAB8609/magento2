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
 * @magentoAppIsolation
 */
class Magento_Test_Integrity_Modular_TemplateFilesTest extends Magento_TestFramework_TestCase_IntegrityAbstract
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
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setDefaultDesignTheme();
        // intentionally to make sure the module files will be requested
        $params = array(
            'area'       => $area,
            'themeModel' => Mage::getModel('Magento\Core\Model\Theme'),
            'module'     => $module
        );
        $file = Magento_TestFramework_Helper_Bootstrap::getObjectmanager()
            ->get('Magento\Core\Model\View\FileSystem')
            ->getFilename($template, $params);
        $this->assertFileExists($file, "Block class: {$class}");
    }

    /**
     * @return array
     */
    public function allTemplatesDataProvider()
    {
        $blockClass = '';
        try {
            /** @var $website \Magento\Core\Model\Website */
            Mage::app()->getStore()->setWebsiteId(0);

            $templates = array();
            foreach (Magento_TestFramework_Utility_Classes::collectModuleClasses('Block') as $blockClass => $module) {
                if (!in_array($module, $this->_getEnabledModules())) {
                    continue;
                }
                $class = new ReflectionClass($blockClass);
                if ($class->isAbstract() || !$class->isSubclassOf('\Magento\Core\Block\Template')) {
                    continue;
                }

                $area = 'frontend';
                if ($module == 'Magento_Install') {
                    $area = 'install';
                } elseif ($module == 'Magento_Adminhtml' || strpos($blockClass, '_Adminhtml_')
                    || strpos($blockClass, '_Backend_')
                    || $class->isSubclassOf('\Magento\Backend\Block\Template'))
                {
                    $area = 'adminhtml';
                }

                Mage::app()->loadAreaPart(
                    \Magento\Core\Model\App\Area::AREA_ADMINHTML,
                    \Magento\Core\Model\App\Area::PART_CONFIG
                );
                Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                    ->get('Magento\Core\Model\Config\Scope')
                    ->setCurrentScope($area);

                $block = Mage::getModel($blockClass);
                $template = $block->getTemplate();
                if ($template) {
                    $templates[$module . ', ' . $template . ', ' . $blockClass . ', ' . $area] =
                        array($module, $template, $blockClass, $area);
                }
            }
            return $templates;
        } catch (Exception $e) {
            trigger_error("Corrupted data provider. Last known block instantiation attempt: '{$blockClass}'."
                . " Exception: {$e}", E_USER_ERROR);
        }
    }
}
