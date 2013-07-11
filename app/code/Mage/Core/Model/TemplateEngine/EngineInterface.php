<?php
/**
 * Interface for Template Engine
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_TemplateEngine_EngineInterface
{
    /**
     * Render the named template in the context of a particular block and with
     * the data provided in $vars.
     *
     * @param Mage_Core_Block_Template $block
     * @param $templateFile
     * @param array $dictionary
     * @return string rendered template
     */
    public function render(Mage_Core_Block_Template $block, $templateFile, array $dictionary = array());
}