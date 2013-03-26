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
 * Abstract theme list
 *
 * @method Mage_Core_Model_Resource_Theme_Collection getCollection()
 * @method Mage_Core_Block_Template setCollection(Mage_Core_Model_Resource_Theme_Collection $collection)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
    extends Mage_Core_Block_Template
{
    /**
     * Application model
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_App $app
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_App $app,
        array $data = array()
    ) {
        $this->_app = $app;
        parent::__construct($context, $data);
    }

    /**
     * Get tab title
     *
     * @return string
     */
    abstract public function getTabTitle();

    /**
     * Add theme buttons
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
     */
    protected function _addThemeButtons($themeBlock)
    {
        $themeBlock->clearButtons();
        return $this;
    }

    /**
     * Get list items of themes
     *
     * @return array
     */
    public function getListItems()
    {
        /** @var $itemBlock Mage_DesignEditor_Block_Adminhtml_Theme */
        $itemBlock = $this->getChildBlock('theme');

        $themeCollection = $this->getCollection();

        $items = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            $itemBlock->setTheme($theme);
            $this->_addThemeButtons($itemBlock);
            $items[] = $this->getChildHtml('theme', false);
        }

        return $items;
    }

    /**
     * Get assign to storeview button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
     */
    protected function _addAssignButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();
        $message = 'You are about to apply this theme for your live store, are you really want to do this?';

        /** @var $assignButton Mage_Backend_Block_Widget_Button */
        $assignButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $assignButton->setData(array(
            'label'   => $this->__('Assign to a Storeview'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'assign',
                        'target' => 'body',
                        'eventData' => array(
                            'theme_id'        => $themeId,
                            'confirm_message' =>  $this->__($message)
                        )
                    ),
                ),
            ),
            'class'   => 'save action-theme-assign primary',
            'target'  => '_blank'
        ));

        $themeBlock->addButton($assignButton);
        return $this;
    }

    /**
     * Get preview button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
     */
    protected function _addPreviewButtonHtml($themeBlock)
    {
        /** @var $previewButton Mage_Backend_Block_Widget_Button */
        $previewButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $previewButton->setData(array(
            'label'     => $this->__('Preview Theme'),
            'class'     => 'action-theme-preview',
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'preview',
                        'target' => 'body',
                        'eventData' => array(
                            'preview_url' => $this->_getPreviewUrl($themeBlock->getTheme()->getId())
                        )
                    ),
                ),
            )
        ));

        $themeBlock->addButton($previewButton);
        return $this;
    }

    /**
     * Get edit button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
     */
    protected function _addEditButtonHtml($themeBlock)
    {
        /** @var $editButton Mage_Backend_Block_Widget_Button */
        $editButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $editButton->setData(array(
            'label'     => $this->__('Edit'),
            'class'     => 'add action-edit',
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'preview',
                        'target' => 'body',
                        'eventData' => array(
                            'preview_url' => $this->_getEditUrl($themeBlock->getTheme()->getId())
                        )
                    ),
                ),
            )
        ));

        $themeBlock->addButton($editButton);
        return $this;
    }

    /**
     * Get preview url for selected theme
     *
     * @param int $themeId
     * @return string
     */
    protected function _getPreviewUrl($themeId)
    {
        return $this->getUrl('*/*/launch', array(
            'theme_id' => $themeId,
            'mode'     => Mage_DesignEditor_Model_State::MODE_NAVIGATION
        ));
    }

    /**
     * Get edit theme url for selected theme
     *
     * @param int $themeId
     * @return string
     */
    protected function _getEditUrl($themeId)
    {
        return $this->getUrl('*/*/launch', array(
            'theme_id' => $themeId,
            'mode'     => Mage_DesignEditor_Model_State::MODE_DESIGN
        ));
    }
}
