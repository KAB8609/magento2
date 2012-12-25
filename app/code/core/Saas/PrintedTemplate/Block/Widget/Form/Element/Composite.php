<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fieldset composite element
 * Draws several child elements in one cell
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Form_Element_Composite
    extends Varien_Data_Form_Element_Abstract
{
    /**
     * Element renderer
     * Used to renderer each composite child element
     *
     * @var Varien_Data_Form_Element_Renderer_Interface
     */
    protected $_elementRenderer;

    /**
     * Initialize element object
     *
     * @param array $attributes
     * @return void
     */
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);

        $this->setType('composite');
    }

    /**
     * Get Element Html
     *
     * @return string
     */
    public function getElementHtml()
    {
        return $this->getChildrenHtml() . $this->getAfterElementHtml();
    }

    /**
     * Collect child elements Html
     *
     * @return string
     */
    public function getChildrenHtml()
    {
        $html = '';
        foreach ($this->getSortedElements() as $element) {
            $html .= $element->toHtml();
        }
        return $html;
    }

    /**
     * Get default Html
     *
     * @return string
     */
    public function getDefaultHtml()
    {
        return $this->getElementHtml();
    }


    /**
     * Add field to fieldset
     * Also apply composite element renderer to element
     *
     * @param string $elementId
     * @param string $type
     * @param array $config
     * @param string $after
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addField($elementId, $type, $config, $after=false)
    {
        return parent::addField($elementId, $type, $config, $after)
            ->setRenderer($this->_getElementRenderer());
    }

    /**
     * Sort elements by sort key
     *
     * @return array
     */
    public function getSortedElements()
    {
        $elements = array();
        // sort children by value by specified key
        if ($this->_sortChildrenByKey) {
            $sortKey = $this->_sortChildrenByKey;
            $uniqueIncrement = 0; // in case if there are elements with same values
            foreach ($this->getElements() as $e) {
                $key = '_' . $uniqueIncrement;
                if ($e->hasData($sortKey)) {
                    $key = $e->getDataUsingMethod($sortKey) . $key;
                }
                $elements[$key] = $e;
                $uniqueIncrement++;
            }
            ksort($elements, $this->_sortChildrenDirection);
            $elements = array_values($elements);
        }
        else {
            foreach ($this->getElements() as $element) {
                $elements[] = $element;
            }
        }
        return $elements;
    }

    /**
     * Get element renderer
     *
     * @return Varien_Data_Form_Element_Renderer_Interface
     */
    protected function _getElementRenderer()
    {
        return Mage::getBlockSingleton('Saas_PrintedTemplate_Widget_Form_Renderer_Fieldset_Composite_Element');
    }
}
