<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recursive renderer that uses several templates
 *
 * @method string getHtml()
 * @method Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Recursive setHtml($html)
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Recursive extends Magento_Backend_Block_Template
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Form element to render
     *
     * @var \Magento\Data\Form\Element\AbstractElement
     */
    protected $_element;

    /**
     * Path to template file in theme.
     *
     * Recursive renderer use '_template' property for rendering templates one by one
     *
     * @var string
     */
    protected $_template = null;

    /**
     * Set of templates to render
     *
     * Upper is rendered first and is inserted into next using <?php echo $this->getHtml() ?>
     *
     * @var array
     */
    protected $_templates = array();

    /**
     * Get element renderer bound to
     *
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Render form element as HTML
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;

        foreach ($this->_templates as $template) {
            $this->setTemplate($template);
            $this->setHtml($this->toHtml());
        }

        return $this->getHtml();
    }
}
