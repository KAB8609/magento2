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
 * Edit button block
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons_Edit
    extends Mage_Backend_Block_Widget_Button_Split
{
    /**
     * @var Mage_DesignEditor_Model_Theme_Context
     */
    protected $_themeContext;

    /**
     * @var Mage_DesignEditor_Model_Theme_ChangeFactory
     */
    protected $_changeFactory;

    /**
     * @var Mage_Core_Model_Date
     */
    protected $_dateModel;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_DesignEditor_Model_Theme_Context $themeContext
     * @param Mage_DesignEditor_Model_Theme_ChangeFactory $changeFactory
     * @param Mage_Core_Model_Date $dateModel
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_DesignEditor_Model_Theme_Context $themeContext,
        Mage_DesignEditor_Model_Theme_ChangeFactory $changeFactory,
        Mage_Core_Model_Date $dateModel,
        array $data = array()
    ) {
        $this->_themeContext = $themeContext;
        $this->_changeFactory = $changeFactory;
        $this->_dateModel = $dateModel;
        parent::__construct($context, $data);
    }

    /**
     * Init edit button
     *
     * @return $this
     */
    public function init()
    {
        $this->_initEditButton();
        return $this;
    }

    /**
     * Retrieve options attributes html
     *
     * @param string $key
     * @param array $option
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getOptionAttributesHtml($key, $option)
    {
        $disabled = isset($option['disabled']) && $option['disabled'] ? 'disabled' : '';
        $title = isset($option['title']) ? $option['title'] : $option['label'];

        $classes = array();
        $classes[] = 'vde_cell_list_item';
        if (!empty($option['default'])) {
            $classes[] = 'checked';
        }
        if ($disabled) {
            $classes[] = $disabled;
        }

        $attributes = $this->_prepareOptionAttributes($option, $title, $classes, $disabled);
        $html = $this->_getAttributesString($attributes);
        $html .= $this->getUiId(isset($option['id']) ? $option['id'] : 'item' . '-' . $key);

        return $html;
    }

    /**
     * Whether button is disabled
     *
     * @return mixed
     */
    public function getDisabled()
    {
        return false;
    }

    /**
     * Disable actions-split functionality if no options provided
     *
     * @return bool
     */
    public function hasSplit()
    {
        $options = $this->getOptions();
        return is_array($options) && count($options) > 0;
    }

    /**
     * Get URL to apply changes from 'staging' theme to 'virtual' theme
     *
     * @param string $revertType
     * @return string
     */
    public function getRevertUrl($revertType)
    {
        return $this->getUrl('*/system_design_editor/revert', array(
            'theme_id'  => $this->_themeContext->getEditableTheme()->getId(),
            'revert_to' => $revertType
        ));
    }

    /**
     * Init 'Edit' button for 'physical' theme
     *
     * @return $this
     */
    protected function _initEditButton()
    {
        $isPhysicalTheme = $this->_themeContext->getEditableTheme()->isPhysical();
        $this->setData(array(
            'label'          => $this->__('Edit'),
            'options'        => array(
                array(
                    'label'          => $this->__('Revert Styles to Last Saved'),
                    'data_attribute' => array('mage-init' => $this->_getDataRevertToPrevious()),
                    'disabled'       => $isPhysicalTheme || !$this->_isAbleRevertToPrevious()
                ),
                array(
                    'label'          => $this->__('Revert Styles to Theme Default Values'),
                    'data_attribute' => array('mage-init' => $this->_getDataRevertToDefault()),
                    'disabled'       => $isPhysicalTheme || !$this->_isAbleRevertToDefault()
                )
            )
        ));

        return $this;
    }

    /**
     * Get json options for button (restore-to-previous)
     *
     * @return string|bool
     */
    protected function _getDataRevertToPrevious()
    {
        $sourceChange = $this->_changeFactory->create();
        $sourceChange->loadByThemeId($this->_themeContext->getEditableTheme()->getId());

        $message = $this->__('Are you sure you want to revert changes to last saved (%s)?',
            $this->_dateModel->date('d-m-Y H:i', $sourceChange->getChangeTime()));

        $data = array(
            'vde-edit-button' => array(
                'event'     => 'revert-to-last',
                'target'    => 'body',
                'eventData' => array(
                    'url'     => $this->getRevertUrl('last_saved'),
                    'confirm' => array('title' => $this->__('Revert Layout to Last Saved'), 'message' => $message),
                )
            )
        );
        return $this->helper('Mage_Backend_Helper_Data')->escapeHtml(json_encode($data));
    }

    /**
     * Get json options for button (restore-to-default)
     *
     * @return string|bool
     */
    protected function _getDataRevertToDefault()
    {
        $message = $this->__('Are you sure you want to revert changes to the Theme defaults?');
        $data = array(
            'vde-edit-button' => array(
                'event'     => 'revert-to-default',
                'target'    => 'body',
                'eventData' => array(
                    'url'     => $this->getRevertUrl('physical'),
                    'confirm' => array('title' => $this->__('Revert Layout to Default'), 'message' => $message)
                )
            )
        );
        return $this->helper('Mage_Backend_Helper_Data')->escapeHtml(json_encode($data));
    }

    /**
     * Check themes by change time (compare staging and virtual theme)
     *
     * @return bool
     */
    protected function _isAbleRevertToPrevious()
    {
        return $this->_hasThemeChanged(
            $this->_themeContext->getStagingTheme(),
            $this->_themeContext->getEditableTheme()
        );
    }

    /**
     * Check themes by change time (compare staging and physical theme)
     *
     * @return bool
     */
    protected function _isAbleRevertToDefault()
    {
        return $this->_hasThemeChanged(
            $this->_themeContext->getStagingTheme(),
            $this->_themeContext->getEditableTheme()->getParentTheme()
        );
    }

    /**
     * Checks themes for changes by time
     *
     * @param Mage_Core_Model_Theme $sourceTheme
     * @param Mage_Core_Model_Theme $targetTheme
     * @return bool
     */
    protected function _hasThemeChanged(Mage_Core_Model_Theme $sourceTheme, Mage_Core_Model_Theme $targetTheme)
    {
        $sourceChange = $this->_changeFactory->create();
        $sourceChange->loadByThemeId($sourceTheme->getId());

        $targetChange = $this->_changeFactory->create();
        $targetChange->loadByThemeId($targetTheme->getId());

        return $sourceChange->getChangeTime() !== $targetChange->getChangeTime();
    }
}
