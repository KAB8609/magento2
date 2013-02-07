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
 * @magentoAppIsolation
 */
class Integrity_Modular_TemplateFilesTest extends Magento_Test_TestCase_IntegrityAbstract
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
        Mage::getDesign()->setDefaultDesignTheme();
        // intentionally to make sure the module files will be requested
        $params = array(
            'area'       => $area,
            'themeModel' => Mage::getModel('Mage_Core_Model_Theme'),
            'module'     => $module
        );
        $file = Mage::getDesign()->getFilename($template, $params);
        $this->assertFileExists($file, "Block class: {$class}");
    }

    /**
     * @return array
     */
    public function allTemplatesDataProvider()
    {
        $blockClass = '';
        try {
            /** @var $website Mage_Core_Model_Website */
            Mage::app()->getStore()->setWebsiteId(0);

            $templates = array();
            foreach (Utility_Classes::collectModuleClasses('Block') as $blockClass => $module) {
                if ($this->_isClassBroken($blockClass)) {
                    continue;
                }
                if (!in_array($module, $this->_getEnabledModules())) {
                    continue;
                }
                $class = new ReflectionClass($blockClass);
                if ($class->isAbstract() || !$class->isSubclassOf('Mage_Core_Block_Template')) {
                    continue;
                }

                $area = 'frontend';
                if ($module == 'Mage_Install') {
                    $area = 'install';
                } elseif ($module == 'Mage_Adminhtml' || strpos($blockClass, '_Adminhtml_')
                    || strpos($blockClass, '_Backend_')
                    || ($this->_isClassInstanceOf($blockClass, 'Mage_Backend_Block_Template'))
                ) {
                    $area = 'adminhtml';
                }

            Mage::app()->loadAreaPart(
                Mage_Core_Model_App_Area::AREA_ADMINHTML, Mage_Core_Model_App_Area::AREA_ADMINHTML
            );
            Mage::getConfig()->setCurrentAreaCode($area);

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

    /**
     * Temporary stub for classes that trigger errors on attempt to instantiate
     *
     * @bug MAGETWO-7377
     * @param string $class
     * @return bool
     */
    private function _isClassBroken($class)
    {
        return in_array($class, array(
            'Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content',
            'Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Tree',
            'Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Uploader',
        ));
    }

    /**
     * @param string $blockClass
     * @param string $parentClass
     * @return bool
     */
    protected function _isClassInstanceOf($blockClass, $parentClass)
    {
        $currentClass = new ReflectionClass($blockClass);
        $supertypes = array();
        do {
            $supertypes = array_merge($supertypes, $currentClass->getInterfaceNames());
            if (!($currentParent = $currentClass->getParentClass())) {
                break;
            }
            $supertypes[] = $currentParent->getName();
            $currentClass = $currentParent;
        } while (true);

        return in_array($parentClass, $supertypes);
    }
}
