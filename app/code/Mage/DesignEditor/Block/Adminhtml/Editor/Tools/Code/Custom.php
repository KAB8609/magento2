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
 * Block that renders Custom tab
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom extends Magento_Backend_Block_Widget_Form
{
    /**
     * Upload file element html id
     */
    const FILE_ELEMENT_NAME = 'css_file_uploader';

    /**
     * @var Mage_DesignEditor_Model_Theme_Context
     */
    protected $_themeContext;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Mage_DesignEditor_Model_Theme_Context $themeContext
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Mage_DesignEditor_Model_Theme_Context $themeContext,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_themeContext = $themeContext;
    }


    /**
     * Create a form element with necessary controls
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array(
            'action'   => '#',
            'method'   => 'post'
        ));
        $this->setForm($form);
        $form->setUseContainer(true);

        $form->addType('css_file', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Uploader');

        $form->addField($this->getFileElementName(), 'css_file', array(
            'name'     => $this->getFileElementName(),
            'accept'   => 'text/css',
            'no_span'  => true
        ));

        parent::_prepareForm();
        return $this;
    }

    /**
     * Get url to download custom CSS file
     *
     * @return string
     */
    public function getDownloadCustomCssUrl()
    {
        return $this->getUrl('*/system_design_theme/downloadCustomCss',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get url to upload custom CSS file
     *
     * @return string
     */
    public function getUploadUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/upload',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get url to save custom CSS file
     *
     * @return string
     */
    public function getSaveCustomCssUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/saveCssContent',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get theme custom css content
     *
     * @param string $targetElementId
     * @param string $contentType
     * @return string
     */
    public function getMediaBrowserUrl($targetElementId, $contentType)
    {
        return $this->getUrl('*/system_design_editor_files/index', array(
            'target_element_id'                           => $targetElementId,
            Mage_Theme_Helper_Storage::PARAM_THEME_ID     => $this->_themeContext->getEditableTheme()->getId(),
            Mage_Theme_Helper_Storage::PARAM_CONTENT_TYPE => $contentType
        ));
    }

    /**
     * Get theme file (with custom CSS)
     *
     * @param Magento_Core_Model_Theme $theme
     * @return Magento_Core_Model_Theme_FileInterface|null
     */
    protected function _getCustomCss($theme)
    {
        $files = $theme->getCustomization()->getFilesByType(
            Mage_Theme_Model_Theme_Customization_File_CustomCss::TYPE
        );
        return reset($files);
    }

    /**
     * Get theme custom CSS content
     *
     * @return null|string
     */
    public function getCustomCssContent()
    {
        $customCss = $this->_getCustomCss($this->_themeContext->getStagingTheme());
        return $customCss ? $customCss->getContent() : null;
    }

    /**
     * Get custom CSS file name
     *
     * @return string|null
     */
    public function getCustomFileName()
    {
        $customCss = $this->_getCustomCss($this->_themeContext->getStagingTheme());
        return $customCss ? $customCss->getFileName() : null;
    }

    /**
     * Get file element name
     *
     * @return string
     */
    public function getFileElementName()
    {
        return self::FILE_ELEMENT_NAME;
    }
}
