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
 * Block that renders Custom tab
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing extends Magento_Backend_Block_Widget_Form
{
    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @var Magento_DesignEditor_Model_Editor_Tools_Controls_Factory
     */
    protected $_controlFactory;

    /**
     * @var Magento_DesignEditor_Model_Theme_Context
     */
    protected $_themeContext;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_DesignEditor_Model_Editor_Tools_Controls_Factory $controlFactory
     * @param Magento_DesignEditor_Model_Theme_Context $themeContext
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Eav_Model_Config $eavConfig,
        Magento_DesignEditor_Model_Editor_Tools_Controls_Factory $controlFactory,
        Magento_DesignEditor_Model_Theme_Context $themeContext,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_eavConfig = $eavConfig;
        $this->_controlFactory = $controlFactory;
        $this->_themeContext = $themeContext;
    }

    /**
     * Returns url to save action of image sizing
     *
     * @return string
     */
    public function getImageSizingUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/saveImageSizing',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Create a form element with necessary controls
     *
     * @return Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array(
            'action'   => '#',
            'method'   => 'post'
        ));
        $form->setId('product_image_sizing_form');
        $this->setForm($form);
        $form->setUseContainer(true);
        $form->setFieldNameSuffix('imagesizing');
        $form->addType('button_button', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Button');

        $isFilePresent = true;
        try{
            /** @var $controlsConfig Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration */
            $controlsConfig = $this->_controlFactory->create(
                Magento_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_IMAGE_SIZING,
                $this->_themeContext->getStagingTheme()
            );
        } catch (Magento_Exception $e) {
            $isFilePresent = false;
        }

        if ($isFilePresent) {
            $this->_initFormElements($controlsConfig, $form);
        } else {
            $hintMessage = __('Sorry, but you cannot resize images for this theme.');
            $form->addField('imagesize-tab-error', 'note', array(
                'after_element_html' => '<p class="error-notice">' . $hintMessage . '</p>'
            ));
        }

        return parent::_prepareForm();
    }

    /**
     * Initialize form elements
     *
     * @param Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration $controlsConfig
     * @param Magento_Data_Form $form
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _initFormElements($controlsConfig, $form)
    {
        $hintMessage =  __('Please enter values for height and width.'
            . ' Use the chain icon if you want height and width to match.'
            . ' Be sure to see how it looks in your store.'
            . ' You may need to update your custom CSS file.');

        $form->addField('information_hint', 'note', array(
            'after_element_html' => '<p class="note">' . $hintMessage . '</p>'));

        $whiteBorder = $controlsConfig->getControlData('product_image_border');
        if ($whiteBorder) {
            $this->_addWhiteBorderElement($whiteBorder);
        }

        $controls = $controlsConfig->getAllControlsData();
        foreach ($controls as $name => $control ) {
            if ($control['type'] != 'image-sizing') {
                continue;
            }
            $this->_addImageSizeFieldset($name, $control);
        }

        $fieldset = $form->addFieldset('save_image_sizing_fieldset', array(
            'name'   => 'save_image_sizing_fieldset',
            'fieldset_type' => 'field',
            'class' => 'save_image_sizing'
        ));
        $this->_addElementTypes($fieldset);

        if ($whiteBorder || $controls) {
            $fieldset->addField('save_image_sizing', 'button_button', array(
                'name'  => 'save_image_sizing',
                'title' => __('Update'),
                'value' => __('Update'),
                'data-mage-init' => $this->helper('Magento_Backend_Helper_Data')->escapeHtml(json_encode(array(
                    'button' => array(
                        'event'  => 'saveForm',
                        'target' => 'body'
                    )
                )))
            ));
        }
        return $this;
    }


    /**
     * Add white border checkbox to form
     *
     * @param array $control
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _addWhiteBorderElement($control)
    {
        /** @var $form Magento_Data_Form */
        $form = $this->getForm();
        $fieldMessage = __('Add white borders to images that are smaller than the container.');
        foreach ($control['components'] as $name => $component) {
            $form->addField('add_white_borders_hidden', 'hidden', array(
                'name'  => $name,
                'value' => '0'
            ));
            $form->addField('add_white_borders', 'checkbox', array(
                'name'    => $name,
                'checked' => !empty($component['value']),
                'value'   => '1',
                'after_element_html' => $fieldMessage
            ));
        }
        /** Get valid message from PO */
        $hintMessage =  __('If an image is too big,'
            . '  we automatically make it smaller and add white borders to fill the container.');
        $form->addField('add_white_borders_hint', 'note', array(
            'after_element_html' => '<p class="description">' . $hintMessage . '</p>'));

        return $this;
    }

    /**
     * Add one image sizing item to form
     *
     * @param string $name
     * @param array $control
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _addImageSizeFieldset($name, $control)
    {
        /** @var $form Magento_Data_Form */
        $form = $this->getForm();
        $fieldset = $form->addFieldset($name, array(
            'name'   => $name,
            'fieldset_type' => 'field',
            'legend' =>  $control['layoutParams']['title']
        ));
        $this->_addElementTypes($fieldset);

        $defaultValues = array();
        foreach ($control['components'] as $componentName => $component) {
            $defaultValues[$componentName] = $component['default'];
            $this->_addFormElement($fieldset, $component, $componentName);
        }
        $this-> _addResetButton($fieldset, $defaultValues, $name);

        return $this;
    }

    /**
     * Add image size form element by component type
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param array $component
     * @param string $componentName
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _addFormElement($fieldset, $component, $componentName)
    {
        switch ($component['type']) {
            case 'image-type':
                $this->_addImageTypeElement($fieldset, $component, $componentName);
                break;
            case 'image-width':
                $this->_addImageWidthElement($fieldset, $component, $componentName);
                break;
            case 'image-ratio':
                $this->_addImageRatioElement($fieldset, $component, $componentName);
                break;
            case 'image-height':
                $this->_addImageHeightElement($fieldset, $component, $componentName);
                break;
        }
        return $this;
    }

    /**
     * Add image type form element to fieldset
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param array $component
     * @param string $componentName
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _addImageTypeElement($fieldset, $component, $componentName)
    {
        $fieldset->addField($componentName, 'select', array(
            'name'   => $componentName,
            'values' => $this->_getSelectOptions(),
            'value'  => $this->_getValue($component)
        ));
        return $this;
    }

    /**
     * Add image width form element to fieldset
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param array $component
     * @param string $componentName
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _addImageWidthElement($fieldset, $component, $componentName)
    {
        $fieldset->addField($componentName, 'text', array(
            'name'   => $componentName,
            'class'  => 'image-width',
            'value'  => $this->_getValue($component),
            'before_element_html' => '<span>W</span>'
        ));
        return $this;
    }

    /**
     * Add image height form element to fieldset
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param array $component
     * @param string $componentName
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _addImageHeightElement($fieldset, $component, $componentName)
    {
        $fieldset->addField($componentName, 'text', array(
            'name'   => $componentName,
            'class'  => 'image-height',
            'value'  => $this->_getValue($component),
            'before_element_html' => '<span>H</span>'
        ));
        return $this;
    }

    /**
     * Add image ratio form element to fieldset
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param array $component
     * @param string $componentName
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _addImageRatioElement($fieldset, $component, $componentName)
    {
        $fieldset->addField($componentName . '-hidden', 'hidden', array(
            'name'  => $componentName,
            'value' => '0'
        ));
        $fieldset->addField($componentName, 'checkbox', array(
            'checked'=> $this->_getValue($component) ? 'checked' : false,
            'name'   => $componentName,
            'class'  => 'image-ratio',
            'value'  => '1',
            'after_element_html' => '<span class="action-connect"></span>'
        ));
        return $this;
    }

    /**
     * Add reset button to fieldset
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param array $defaultValues
     * @param string $name
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_ImageSizing
     */
    protected function _addResetButton($fieldset, $defaultValues, $name)
    {
        $fieldset->addField($name . '_reset', 'button_button', array(
            'name'  => $name . '_reset',
            'title' => __('Reset to Original'),
            'value' => __('Reset to Original'),
            'class' => 'action-reset',
            'data-mage-init' => $this->helper('Magento_Backend_Helper_Data')->escapeHtml(json_encode(array(
                'button' => array(
                    'event'     => 'restoreDefaultData',
                    'target'    => 'body',
                    'eventData' => $defaultValues
        ))))));
        return $this;
    }

    /**
     * Get value
     *
     * @param array $component
     * @return array
     */
    protected function _getValue($component)
    {
        return $component['value'] !== false ? $component['value'] : $component['default'];
    }

    /**
     * Return values for select element
     *
     * @return array
     */
    protected function _getSelectOptions()
    {
        $options = array();
        foreach ($this->getImageTypes() as $imageType) {
            $attribute = $this->_eavConfig->getAttribute('catalog_product', $imageType);
            $options[] = array(
                'value' => $imageType,
                'label' => $attribute->getFrontendLabel()
            );
        }
        return $options;
    }

    /**
     * Return product image types
     *
     * @return array
     */
    public function getImageTypes()
    {
        return array('image', 'small_image', 'thumbnail');
    }

    /**
     * Set additional form button
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array('button_button' => 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Button');
    }
}