<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Composite form element renderer
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Composite
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Recursive
{
    /**
     * Set of templates to render
     *
     * Upper is rendered first and is inserted into next using <?php echo $this->getHtml() ?>
     * This templates are made of fieldset.phtml but split into several templates
     *
     * @var array
     */
    protected $_templates = array(
        'Mage_DesignEditor::editor/form/renderer/composite/children.phtml',
        'Mage_DesignEditor::editor/form/renderer/composite.phtml',
        'Mage_DesignEditor::editor/form/renderer/composite/wrapper.phtml',
    );

    /**
     * Get CSS classes for element
     *
     * Used in composite.phtml
     *
     * @return array
     */
    public function getCssClasses()
    {
        /** @var $element Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract */
        $element = $this->getElement();
        $isField = $element->getFieldsetType() == 'field';

        $cssClasses = array();
        $cssClasses[] = ($isField) ? 'field' : 'fieldset';
        if ($element->getClass()) {
            $cssClasses[] = $element->getClass();
        }
        if ($isField && $element->hasAdvanced()) {
            $cssClasses[] = 'complex';
        }

        return $cssClasses;
    }
}
