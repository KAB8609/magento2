<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme form, general tab
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_General
    extends Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_TabAbstract
{
    /**
     * Whether theme is editable
     *
     * @var bool
     */
    protected $_isThemeEditable = false;

    /**
     * @var Magento_Core_Model_Theme_Image_Path
     */
    protected $_themeImagePath;

    /**
     * @var Magento_File_Size
     */
    protected $_fileSize;

    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Theme_Image_Path $themeImagePath
     * @param Magento_File_Size $fileSize
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Theme_Image_Path $themeImagePath,
        Magento_File_Size $fileSize,
        array $data = array()
    ) {
        $this->_themeImagePath = $themeImagePath;
        $this->_fileSize = $fileSize;
        parent::__construct($formFactory, $coreData, $context, $registry, $objectManager, $data);
    }

    /**
     * Create a form element with necessary controls
     *
     * @return Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_General|Magento_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        /** @var $session Magento_Backend_Model_Session */
        $session = $this->_objectManager->get('Magento_Backend_Model_Session');
        $formDataFromSession = $session->getThemeData();
        $this->_isThemeEditable = $this->_getCurrentTheme()->isEditable();
        $formData = $this->_getCurrentTheme()->getData();
        if ($formDataFromSession && isset($formData['theme_id'])) {
            unset($formDataFromSession['preview_image']);
            $formData = array_merge($formData, $formDataFromSession);
            $session->setThemeData(null);
        }
        $this->setIsThemeExist(isset($formData['theme_id']));

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $this->_addThemeFieldset($form, $formData);

        if (!$this->getIsThemeExist()) {
            $formData = array_merge($formData, $this->_getDefaults());
        }
        $form->addValues($formData);
        $form->setFieldNameSuffix('theme');
        $this->setForm($form);

        return $this;
    }

    /**
     * Add theme fieldset
     *
     * @param Magento_Data_Form $form
     * @param array $formData
     * @return Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_General
     */
    protected function _addThemeFieldset($form, $formData)
    {
        $themeFieldset = $form->addFieldset('theme', array(
            'legend'   => __('Theme Settings'),
        ));
        $this->_addElementTypes($themeFieldset);

        if (isset($formData['theme_id'])) {
            $themeFieldset->addField('theme_id', 'hidden', array(
                'name' => 'theme_id'
            ));
        }

        /** @var $themesCollections Magento_Core_Model_Theme_Collection */
        $themesCollections = $this->_objectManager->create('Magento_Core_Model_Theme_Collection');

        /** @var $helper Magento_Core_Helper_Data */
        $helper = $this->_objectManager->get('Magento_Core_Helper_Data');

        $onChangeScript = sprintf('parentThemeOnChange(this.value, %s)', str_replace(
            '"', '\'', $helper->jsonEncode($this->_getDefaultsInherited($themesCollections->addDefaultPattern()))
        ));

        /** @var $parentTheme Magento_Core_Model_Theme */
        $parentTheme = $this->_objectManager->create('Magento_Core_Model_Theme');
        if (!empty($formData['parent_id'])) {
            $parentTheme->load($formData['parent_id']);
        }

        if ($this->_getCurrentTheme()->isObjectNew()) {
            $themeFieldset->addField('parent_id', 'select', array(
                'label'    => __('Parent Theme'),
                'title'    => __('Parent Theme'),
                'name'     => 'parent_id',
                'values'   => $themesCollections->toOptionArray(!$parentTheme->getId()),
                'required' => true,
                'class'    => 'no-changes',
                'onchange' => $onChangeScript
            ));
        } else if (!empty($formData['parent_id'])) {
            $themeFieldset->addField('parent_title', 'note', array(
                'label'    => __('Parent Theme'),
                'title'    => __('Parent Theme'),
                'name'     => 'parent_title',
                'text'     => $parentTheme->getId() ? $parentTheme->getThemeTitle() : ''
            ));
        }

        if (!empty($formData['theme_path'])) {
            $themeFieldset->addField('theme_path', 'label', array(
                'label'    => __('Theme Path'),
                'title'    => __('Theme Path'),
                'name'     => 'theme_code',
            ));
        }

        $themeFieldset->addField('theme_version', $this->_getFieldTextType(), array(
            'label'    => __('Theme Version'),
            'title'    => __('Theme Version'),
            'name'     => 'theme_version',
            'required' => $this->_isFieldAttrRequired(),
            'note'     => $this->_filterFieldNote(__('Example: 0.0.0.1 or 123.1.0.25-alpha1'))
        ));

        $themeFieldset->addField('theme_title', $this->_getFieldTextType(), array(
            'label'    => __('Theme Title'),
            'title'    => __('Theme Title'),
            'name'     => 'theme_title',
            'required' => $this->_isFieldAttrRequired()
        ));

        if ($this->_isThemeEditable) {
            $themeFieldset->addField('preview_image', 'image', array(
                'label'    => __('Theme Preview Image'),
                'title'    => __('Theme Preview Image'),
                'name'     => 'preview',
                'required' => false,
                'note'     => $this->_getPreviewImageNote()
            ));
        } else if (!empty($formData['preview_image'])) {
            $themeFieldset->addField('preview_image', 'note', array(
                'label'    => __('Theme Preview Image'),
                'title'    => __('Theme Preview Image'),
                'name'     => 'preview',
                'after_element_html' => '<img width="50" src="' . $this->_themeImagePath->getPreviewImageDirectoryUrl()
                    . $formData['preview_image'] . '" />'
            ));
        }

        return $this;
    }

    /**
     * No field notes if theme is not editable
     *
     * @param $text
     * @return string
     */
    protected function _filterFieldNote($text)
    {
        return $this->_isThemeEditable ? $text : '';
    }

    /**
     * Field is not marked as required if theme is not editable
     *
     * @return bool
     */
    protected function _isFieldAttrRequired()
    {
        return $this->_isThemeEditable ? true : false;
    }

    /**
     * Text field replaced to label if theme is not editable
     *
     * @return string
     */
    protected function _getFieldTextType()
    {
        return $this->_isThemeEditable ? 'text' : 'label';
    }

    /**
     * Set additional form field type for theme preview image
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $element =
            'Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_Image';
        return array('image' => $element);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Get theme default values
     *
     * @return array
     */
    protected function _getDefaults()
    {
        $defaults = array();
        $defaults['theme_version'] = '0.0.0.1';
        $defaults['theme_title'] = __('New Theme');

        return $defaults;
    }

    /**
     * Get theme default values while inheriting other theme
     *
     * @param $themesCollections
     * @return array
     */
    protected function _getDefaultsInherited($themesCollections)
    {
        $data = array('' => $this->_getDefaults());

        /** @var $theme Magento_Core_Model_Theme */
        foreach ($themesCollections as $theme) {
            $theme->load($theme->getThemePath(), 'theme_path');
            if (!$theme->getId()) {
                continue;
            }
            $data[$theme->getId()] = array(
                'theme_title'          => __('Copy of %1', $theme->getThemeTitle())
            );
        }

        return $data;
    }

    /**
     * Get note string for theme's preview image
     *
     * @return string
     */
    protected function _getPreviewImageNote()
    {
        $maxImageSize = $this->_fileSize->getMaxFileSizeInMb();
        if ($maxImageSize) {
            return __('Max image size %1M', $maxImageSize);
        } else {
            return __('Something is wrong with the file upload settings.');
        }
    }
}
