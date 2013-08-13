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
 * Parent composite form element for VDE
 *
 * This elements know about renderer factory and use it to set renders to its children
 *
 * @method array getComponents()
 * @method string getFieldsetContainerId()
 * @method bool getCollapsable()
 * @method string getHeaderBar()
 * @method string getLegend()
 * @method string getFieldsetType()
 * @method string getAdvancedPosition()
 * @method string getNoContainer()
 * @method string getComment()
 * @method string getClass()
 * @method bool hasHtmlContent()
 * @method string getHtmlContent()
 * @method string getLabel()
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract setLegend($legend)
 */
abstract class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
    extends Magento_Data_Form_Element_Fieldset
    implements Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ContainerInterface
{
    /**
     * Delimiter for name parts in composite controls
     */
    const CONTROL_NAME_DELIMITER = ':';

    /**
     * Factory that creates renderer for element by element class
     *
     * @var Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory
     */
    protected $_rendererFactory;

    /**
     * Factory that creates element by element type
     *
     * @var Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Element_Factory
     */
    protected $_elementsFactory;

    /**
     * Helper
     *
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Element_Factory $elementsFactory
     * @param Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory $rendererFactory
     * @param Mage_DesignEditor_Helper_Data $helper
     * @param array $attributes
     */
    public function __construct(
        Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Element_Factory $elementsFactory,
        Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory $rendererFactory,
        Mage_DesignEditor_Helper_Data $helper,
        $attributes = array()
    ) {
        $this->_elementsFactory = $elementsFactory;
        $this->_rendererFactory = $rendererFactory;
        $this->_helper = $helper;

        parent::__construct($attributes);
    }

    /**
     * Constructor helper
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setLegend($this->getLabel());

        $this->_addElementTypes();
        $this->_addFields();

        $this->addClass('element-' . static::CONTROL_TYPE);
    }

    /**
     * Add fields to composite composite element
     *
     * @param string $elementId
     * @param string $type
     * @param array $config
     * @param boolean $after
     * @param boolean $isAdvanced
     * @return Magento_Data_Form_Element_Abstract
     */
    public function addField($elementId, $type, $config, $after = false, $isAdvanced = false)
    {
        if (isset($this->_types[$type])) {
            $className = $this->_types[$type];
        } else {
            $className = 'Magento_Data_Form_Element_' . ucfirst(strtolower($type));
        }
        $element = $this->_elementsFactory->create($className, $config);
        $element->setId($elementId);
        $this->addElement($element, $after);

        $layoutName = $element->getId() . '-renderer';
        try {
            $renderer = $this->_rendererFactory->create($className, $layoutName);
        } catch (Magento_Core_Exception $e) {
            $renderer = null;
        }
        if ($renderer) {
            $element->setRenderer($renderer);
        }
        $element->setAdvanced($isAdvanced);
        return $element;
    }

    /**
     * Get controls component of given type
     *
     * @param string $type
     * @param string|null $subtype
     * @return array
     * @throws Magento_Core_Exception
     */
    public function getComponent($type, $subtype = null)
    {
        $components = $this->getComponents();
        $componentId = $this->getComponentId($type);
        if (!isset($components[$componentId])) {
            throw new Magento_Core_Exception($this->_helper->__(
                'Component of the type "%s" is not found between elements of "%s"', $type, $this->getData('name')
            ));
        }
        $component = $components[$componentId];

        if ($subtype) {
            $subComponentId = $this->getComponentId($subtype);
            $component = $component['components'][$subComponentId];
        }

        return $component;
    }

    /**
     * Get id that component of given type should have
     *
     * @param string $type
     * @return string
     */
    public function getComponentId($type)
    {
        $names = explode(self::CONTROL_NAME_DELIMITER, $this->getData('name'));
        return join('', array(array_shift($names), self::CONTROL_NAME_DELIMITER, $type));
    }

    /**
     * Add form elements
     *
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
     */
    abstract protected function _addFields();

    /**
     * Add element types used in composite font element
     *
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
     */
    abstract protected function _addElementTypes();
}
