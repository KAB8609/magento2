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
 * Block that renders Design tab
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_QuickStyles
    extends Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs_Abstract
{
    /**
     * Tab HTML identifier
     */
    protected $_htmlId = 'vde-tab-quick-styles';

    /**
     * Tab HTML title
     */
    protected $_title = 'Quick Styles';

    /**
     * Get tabs data
     *
     * @return array
     */
    public function getTabs()
    {
        return array(
            array(
                'is_active'     => true,
                'id'          => 'vde-tab-header',
                'title'         => strtoupper($this->__('Header')),
                'content_block' => 'design_editor_tools_quick-styles_header'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-bgs',
                'title'         => strtoupper($this->__('Backgrounds')),
                'content_block' => 'design_editor_tools_quick-styles_backgrounds'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-buttons',
                'title'         => strtoupper($this->__('Buttons & Icons')),
                'content_block' => 'design_editor_tools_quick-styles_buttons'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-tips',
                'title'         => strtoupper($this->__('Tips & Messages')),
                'content_block' => 'design_editor_tools_quick-styles_tips'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-fonts',
                'title'         => strtoupper($this->__('Fonts')),
                'content_block' => 'design_editor_tools_quick-styles_fonts'
            ),

        );
    }

    /**
     * Get the tab state
     *
     * Active tab is showed, while inactive tabs are hidden
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsActive()
    {
        return true;
    }
}
