<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Data form
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method \Magento\Data\Form setParent($block)
 * @method Magento_Backend_Block_Widget_Form getParent()
 * @method Magento_Backend_Block_Widget_Form setUseContainer($flag)
 */
namespace Magento\Data;

class Form extends \Magento\Data\Form\AbstractForm
{
    /**
     * All form elements collection
     *
     * @var \Magento\Data\Form\Element\Collection
     */
    protected $_allElements;

    /**
     * form elements index
     *
     * @var array
     */
    protected $_elementsIndex;

    static protected $_defaultElementRenderer;
    static protected $_defaultFieldsetRenderer;
    static protected $_defaultFieldsetElementRenderer;

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->_allElements = new \Magento\Data\Form\Element\Collection($this);
    }

    public static function setElementRenderer(\Magento\Data\Form\Element\Renderer\RendererInterface $renderer = null)
    {
        self::$_defaultElementRenderer = $renderer;
    }

    public static function setFieldsetRenderer(\Magento\Data\Form\Element\Renderer\RendererInterface $renderer = null)
    {
        self::$_defaultFieldsetRenderer = $renderer;
    }

    public static function setFieldsetElementRenderer(\Magento\Data\Form\Element\Renderer\RendererInterface $renderer = null)
    {
        self::$_defaultFieldsetElementRenderer = $renderer;
    }

    public static function getElementRenderer()
    {
        return self::$_defaultElementRenderer;
    }

    public static function getFieldsetRenderer()
    {
        return self::$_defaultFieldsetRenderer;
    }

    public static function getFieldsetElementRenderer()
    {
        return self::$_defaultFieldsetElementRenderer;
    }

    /**
     * Return allowed HTML form attributes
     * @return array
     */
    public function getHtmlAttributes()
    {
        return array('id', 'name', 'method', 'action', 'enctype', 'class', 'onsubmit', 'target');
    }

    /**
     * Add form element
     *
     * @param   \Magento\Data\Form\Element\AbstractElement $element
     * @return  \Magento\Data\Form
     */
    public function addElement(\Magento\Data\Form\Element\AbstractElement $element, $after=false)
    {
        $this->checkElementId($element->getId());
        parent::addElement($element, $after);
        $this->addElementToCollection($element);
        return $this;
    }

    /**
     * Check existing element
     *
     * @param   string $elementId
     * @return  bool
     */
    protected function _elementIdExists($elementId)
    {
        return isset($this->_elementsIndex[$elementId]);
    }

    public function addElementToCollection($element)
    {
        $this->_elementsIndex[$element->getId()] = $element;
        $this->_allElements->add($element);
        return $this;
    }

    public function checkElementId($elementId)
    {
        if ($this->_elementIdExists($elementId)) {
            throw new \Exception('Element with id "'.$elementId.'" already exists');
        }
        return true;
    }

    public function getForm()
    {
        return $this;
    }

    public function getElement($elementId)
    {
        if ($this->_elementIdExists($elementId)) {
            return $this->_elementsIndex[$elementId];
        }
        return null;
    }

    public function setValues($values)
    {
        foreach ($this->_allElements as $element) {
            if (isset($values[$element->getId()])) {
                $element->setValue($values[$element->getId()]);
            }
            else {
                $element->setValue(null);
            }
        }
        return $this;
    }

    public function addValues($values)
    {
        if (!is_array($values)) {
            return $this;
        }
        foreach ($values as $elementId=>$value) {
            if ($element = $this->getElement($elementId)) {
                $element->setValue($value);
            }
        }
        return $this;
    }

    /**
     * Add suffix to name of all elements
     *
     * @param string $suffix
     * @return \Magento\Data\Form
     */
    public function addFieldNameSuffix($suffix)
    {
        foreach ($this->_allElements as $element) {
            $name = $element->getName();
            if ($name) {
                $element->setName($this->addSuffixToName($name, $suffix));
            }
        }
        return $this;
    }

    public function addSuffixToName($name, $suffix)
    {
        if (!$name) {
            return $suffix;
        }
        $vars = explode('[', $name);
        $newName = $suffix;
        foreach ($vars as $index=>$value) {
            $newName.= '['.$value;
            if ($index==0) {
                $newName.= ']';
            }
        }
        return $newName;
    }

    public function removeField($elementId)
    {
        if ($this->_elementIdExists($elementId)) {
            unset($this->_elementsIndex[$elementId]);
        }
        return $this;
    }

    public function setFieldContainerIdPrefix($prefix)
    {
        $this->setData('field_container_id_prefix', $prefix);
        return $this;
    }

    public function getFieldContainerIdPrefix()
    {
        return $this->getData('field_container_id_prefix');
    }

    public function toHtml()
    {
        \Magento\Profiler::start('form/toHtml');
        $html = '';
        if ($useContainer = $this->getUseContainer()) {
            $html .= '<form '.$this->serialize($this->getHtmlAttributes()).'>';
            $html .= '<div>';
            if (strtolower($this->getData('method')) == 'post') {
                $html .= '<input name="form_key" type="hidden" value="'.\Mage::getSingleton('Magento_Core_Model_Session')->getFormKey().'" />';
            }
            $html .= '</div>';
        }

        foreach ($this->getElements() as $element) {
            $html.= $element->toHtml();
        }

        if ($useContainer) {
            $html.= '</form>';
        }
        \Magento\Profiler::stop('form/toHtml');
        return $html;
    }

    public function getHtml()
    {
        return $this->toHtml();
    }
}
