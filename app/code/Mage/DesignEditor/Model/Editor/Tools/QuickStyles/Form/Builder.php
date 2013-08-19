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
 * VDE area model
 */
class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Builder
{
    /**
     * @var Magento_Data_Form_Factory
     */
    protected $_formFactory;

    /**
     * @var Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory
     */
    protected $_rendererFactory;

    /**
     * @var Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Element_Factory
     */
    protected $_elementsFactory;

    /**
     * @var Mage_DesignEditor_Model_Editor_Tools_Controls_Factory
     */
    protected $_configFactory;

    /**
     * @var Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    protected $_config;

    /**
     * Constructor
     *
     * @param Magento_Data_Form_Factory $formFactory
     * @param Mage_DesignEditor_Model_Editor_Tools_Controls_Factory $configFactory
     * @param Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory $rendererFactory
     * @param Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Element_Factory $elementsFactory
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Mage_DesignEditor_Model_Editor_Tools_Controls_Factory $configFactory,
        Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Renderer_Factory $rendererFactory,
        Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Form_Element_Factory $elementsFactory
    ) {
        $this->_formFactory     = $formFactory;
        $this->_configFactory   = $configFactory;
        $this->_rendererFactory = $rendererFactory;
        $this->_elementsFactory = $elementsFactory;
    }

    /**
     * Create varien data form with provided params
     *
     * @param array $data
     * @return Magento_Data_Form
     * @throws InvalidArgumentException
     */
    public function create(array $data = array())
    {
        $isFilePresent = true;
        try {
            $this->_config = $this->_configFactory->create(
                Mage_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES,
                $data['theme'],
                $data['parent_theme']
            );
        } catch (Magento_Exception $e) {
            $isFilePresent = false;
        }

        if (!isset($data['tab'])) {
            throw new InvalidArgumentException((sprintf('Invalid controls tab "%s".', $data['tab'])));
        }

        if ($isFilePresent) {
            /** @var $form Magento_Data_Form */
            $form = $this->_formFactory->create($data);

            $this->_addElementTypes($form);

            $columns = $this->_initColumns($form, $data['tab']);
            $this->_populateColumns($columns, $data['tab']);
        } else {
            $form = new Magento_Data_Form(array('action' => '#'));
        }

        if ($this->_isFormEmpty($form)) {
            $hintMessage = __('Sorry, but you cannot edit these theme styles.');
            $form->addField($data['tab'] . '-tab-error', 'note', array(
                'after_element_html' => '<p class="error-notice">' . $hintMessage . '</p>'
            ), '^');
        }
        return $form;
    }

    /**
     * Check is any elements present in form
     *
     * @param Magento_Data_Form $form
     * @return bool
     */
    protected function _isFormEmpty($form)
    {
        $isEmpty = true;
        /** @var  $elements Magento_Data_Form_Element_Collection */
        $elements = $form->getElements();
        foreach ($elements as $element) {
            if ($element->getElements()->count()) {
                $isEmpty = false;
                break;
            }
        }
        return $isEmpty;
    }

    /**
     * Add column elements to form
     *
     * @param Magento_Data_Form $form
     * @param string $tab
     * @return array
     */
    protected function _initColumns($form, $tab)
    {
        /** @var $columnLeft Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column */
        $columnLeft = $form->addField('column-left-' . $tab, 'column', array());
        $columnLeft->setRendererFactory($this->_rendererFactory)
            ->setElementsFactory($this->_elementsFactory);

        /** @var $columnMiddle Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column */
        $columnMiddle = $form->addField('column-middle-' . $tab, 'column', array());
        $columnMiddle->setRendererFactory($this->_rendererFactory)
            ->setElementsFactory($this->_elementsFactory);

        /** @var $columnRight Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column */
        $columnRight = $form->addField('column-right-' . $tab, 'column', array());
        $columnRight->setRendererFactory($this->_rendererFactory)
            ->setElementsFactory($this->_elementsFactory);

        $columns = array(
            'left'   => $columnLeft,
            'middle' => $columnMiddle,
            'right'  => $columnRight
        );

        return $columns;
    }

    /**
     * Populate columns with fields
     *
     * @param array $columns
     * @param string $tab
     */
    protected function _populateColumns($columns, $tab)
    {
        foreach ($this->_config->getAllControlsData() as $id => $control) {
            $positionData = $control['layoutParams'];
            unset($control['layoutParams']);

            if ($positionData['tab'] != $tab) {
                continue;
            }

            $config = $this->_buildElementConfig($id, $positionData, $control);

            /** @var $column Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column */
            $column = $columns[$positionData['column']];
            $column->addField($id, $control['type'], $config);
        }
    }

    /**
     * Create form element config
     *
     * @param string $htmlId
     * @param array $positionData
     * @param array $control
     * @return array
     */
    protected function _buildElementConfig($htmlId, $positionData, $control)
    {
        $label = __($positionData['title']);

        $config = array(
            'name'  => $htmlId,
            'label' => $label,
        );
        if (isset($control['components'])) {
            $config['components'] = $control['components'];
            $config['title'] = $label;
        } else {
            $config['value'] = $control['value'];
            $config['title'] = htmlspecialchars(sprintf('%s {%s: %s}',
                $control['selector'],
                $control['attribute'],
                $control['value']
            ), ENT_COMPAT);
            if (isset($control['options'])) {
                $config['options'] =  $control['options'];
            }
        }

        return $config;
    }

    /**
     * Add custom element types
     *
     * @param Magento_Data_Form $form
     */
    protected function _addElementTypes($form)
    {
        $form->addType('column', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Column');
    }
}
