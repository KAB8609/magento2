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
 * @group integrity
 */
class Integrity_Theme_TemplateFilesTest extends Magento_Test_TestCase_IntegrityAbstract
{
    /**
     * Note that data provider is not used in conventional way in order to not overwhelm test statistics
     */
    public function testTemplates()
    {
        $invalidTemplates = array();
        foreach ($this->templatesDataProvider() as $template) {
            list($area, $package, $theme, $module, $file, $xml) = $template;
            $params = array(
                '_area'     => $area,
                '_package'  => $package,
                '_theme'    => $theme,
                '_module'   => $module
            );
            try {
                $templateFilename = Mage::getDesign()->getTemplateFilename($file, $params);
                $this->assertFileExists($templateFilename);
            } catch (PHPUnit_Framework_ExpectationFailedException $e) {
                if ('frontend' == $area && (0 === strpos($file, 'banner')  || 0 === strpos($file, 'catalogevent')
                    || 0 === strpos($file, 'customerbalance') || 0 === strpos($file, 'giftcard')
                    || 0 === strpos($file, 'giftcardaccount') || 0 === strpos($file, 'giftregistry')
                    || 0 === strpos($file, 'giftwrapping') || 0 === strpos($file, 'invitation')
                    || 0 === strpos($file, 'pagecache') || 0 === strpos($file, 'pbridge')
                    || 0 === strpos($file, 'reward') || 0 === strpos($file, 'salespool')
                    || 0 === strpos($file, 'targetrule') || 0 === strpos($file, 'rma')
                )) {
                    continue; // temporary crutch-fix, while templates weren't relocated under modules
                }
                $invalidTemplates[] = "{$templateFilename}\n"
                    . "Parameters: {$area}/{$package}/{$theme} {$module}::{$file}\nLayout update: {$xml}";
            }
        }

        $this->assertEmpty($invalidTemplates, "Invalid templates found:\n\n" . implode("\n-----\n", $invalidTemplates));
        $this->markTestIncomplete('Remove crutch-fix in MAGETWO-513');
    }

    public function templatesDataProvider()
    {
        $templates = array();

        $themes = $this->_getDesignThemes();
        foreach ($themes as $view) {
            list($area, $package, $theme) = explode('/', $view);
            $layoutUpdate = new Mage_Core_Model_Layout_Update();
            $xml = $layoutUpdate->getFileLayoutUpdatesXml($area, $package, $theme);
            $layoutTemplates = $this->_getLayoutTemplates($xml);
            foreach ($layoutTemplates as $templateData) {
                $templates[] = array_merge(array($area, $package, $theme), $templateData);
            }
        }

        return $templates;
    }

    /**
     * Get templates list that are defined in layout
     *
     * @param  SimpleXMLElement $layoutXml
     * @return array
     */
    protected function _getLayoutTemplates($layoutXml)
    {
        $templates = array();

        $blocks = $layoutXml->xpath('//block');
        foreach ($blocks as $block) {
            $attributes = $block->attributes();
            if (isset($attributes['template'])) {
                $module = $this->_getBlockModule($block);
                if (!$this->_isTemplateForDisabledModule($module, (string)$attributes['template'])) {
                    $templates[] = array($module, (string)$attributes['template'], $block->asXML());
                }
            }
        }

        $layoutTemplates = $layoutXml->xpath('//template');
        foreach ($layoutTemplates as $template) {
            $action = $template->xpath("parent::*");
            $attributes = $action[0]->attributes();
            switch ($attributes['method']) {
                case 'setTemplate':
                    $parent = $action[0]->xpath("parent::*");
                    $attributes = $parent[0]->attributes();
                    $referenceName = (string) $attributes['name'];
                    $block = $layoutXml->xpath("//block[@name='".$referenceName."']");
                    $module = $this->_getBlockModule($block[0]);
                    if (!$template->attributes() && !$this->_isTemplateForDisabledModule($module, (string)$template)) {
                        $templates[] = array($module, (string)$template, $parent[0]->asXml());
                    }
                    break;
                case 'addPriceBlockType':
                case 'addRowItemRender':
                case 'addItemRender':
                case 'addOptionRenderer':
                case 'addInformationRenderer':
                case 'addMergeSettingsBlockType':
                    $blockType = $action[0]->xpath('block');
                    $module = $this->_getBlockModule($blockType[0]);
                    if (!$this->_isTemplateForDisabledModule($module, (string)$template)) {
                        $templates[] = array($module, (string)$template, $action[0]->asXml());
                    }
                    break;
                default:
                    break;
            }
        }
        return $templates;
    }

    /**
     * Get module name based on block definition in xml layout
     *
     * @param  SimpleXMLElement $xmlNode
     * @return string
     */
    protected function _getBlockModule($xmlNode)
    {
        $attributes = $xmlNode->attributes();
        if (isset($attributes['type'])) {
            $class = Mage::getConfig()->getBlockClassName($attributes['type']);
        } else {
            $class = Mage::getConfig()->getBlockClassName((string) $xmlNode);
        }
        $blockModule = substr($class, 0, strpos($class, '_Block'));
        return $blockModule;
    }

    /**
     * Returns whether template belongs to a disabled module
     *
     * @param string $blockModule Module of a block that will render this template
     * @param string $template
     * @return bool
     */
    protected function _isTemplateForDisabledModule($blockModule, $template)
    {
        $enabledModules = $this->_getEnabledModules();

        if (!isset($enabledModules[$blockModule])) {
            return true;
        }
        return $this->_isFileForDisabledModule($template);
    }
}
