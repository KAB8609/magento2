<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

// @codingStandardsIgnoreStart
/**
 * Abstract theme list
 *
 * @method \Magento\Core\Model\Resource\Theme\Collection getCollection()
 * @method bool|null getIsFirstEntrance()
 * @method bool|null getHasThemeAssigned()
 * @method \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList setHasThemeAssigned(bool $flag)
 * @method \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList|\Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\Available setCollection(\Magento\Core\Model\Resource\Theme\Collection $collection)
 * @method \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList setIsFirstEntrance(bool $flag)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
// @codingStandardsIgnoreEnd
namespace Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList;

abstract class AbstractSelectorList
    extends \Magento\Backend\Block\Template
{
    /**
     * Get tab title
     *
     * @return string
     */
    abstract public function getTabTitle();

    /**
     * Add theme buttons
     *
     * @param \Magento\DesignEditor\Block\Adminhtml\Theme $themeBlock
     * @return \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList
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
        /** @var $itemBlock \Magento\DesignEditor\Block\Adminhtml\Theme */
        $itemBlock = $this->getChildBlock('theme');
        $themeCollection = $this->getCollection();

        $items = array();
        if (!empty($themeCollection)) {
            /** @var $theme \Magento\Core\Model\Theme */
            foreach ($themeCollection as $theme) {
                $itemBlock->setTheme($theme);
                $this->_addThemeButtons($itemBlock);
                $items[] = $this->getChildHtml('theme', false);
            }
        }
        return $items;
    }

    /**
     * Add duplicate button
     *
     * @param \Magento\DesignEditor\Block\Adminhtml\Theme $themeBlock
     * @return $this
     */
    protected function _addDuplicateButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();

        /** @var $assignButton \Magento\Backend\Block\Widget\Button */
        $assignButton = $this->getLayout()->createBlock('Magento\DesignEditor\Block\Adminhtml\Theme\Button');
        $assignButton->setData(array(
            'title' => __('Duplicate'),
            'label' => __('Duplicate'),
            'class'   => 'action-duplicate',
            'href'   => $this->getUrl('*/*/duplicate', array('theme_id' => $themeId))
        ));

        $themeBlock->addButton($assignButton);
        return $this;
    }

    /**
     * Get assign to store-view button
     *
     * This button used on "Available Themes" tab and "My Customizations" tab
     *
     * @param \Magento\DesignEditor\Block\Adminhtml\Theme $themeBlock
     * @return $this
     */
    protected function _addAssignButtonHtml($themeBlock)
    {
        if ($this->getHasThemeAssigned()) {
            // @codingStandardsIgnoreStart
            $message = __('You chose a new theme for your live store. Click "OK" to replace your current theme.');
            // @codingStandardsIgnoreEnd
        } else {
            // @codingStandardsIgnoreStart
            $message = __('You chose a theme for your new store. Click "OK" to go live. You can always modify or switch themes in "My Customizations" and "Available Themes."');
            // @codingStandardsIgnoreEnd
        }
        $themeId = $themeBlock->getTheme()->getId();

        /** @var $assignButton \Magento\Backend\Block\Widget\Button */
        $assignButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');
        $assignButton->setData(array(
            'label'   => __('Assign to a Store View'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array(
                        'event'     => 'assign',
                        'target'    => 'body',
                        'eventData' => array(
                            'theme_id' => $themeId,
                            'confirm'  => array(
                                'message' =>  $message,
                                'title'   =>  __('Assign New Theme')
                            )
                        )
                    ),
                ),
            ),
            'class'   => 'save action-theme-assign primary',
        ));

        $themeBlock->addButton($assignButton);
        return $this;
    }

    /**
     * Get edit button
     *
     * @param \Magento\DesignEditor\Block\Adminhtml\Theme $themeBlock
     * @return $this
     */
    protected function _addEditButtonHtml($themeBlock)
    {
        /** @var $editButton \Magento\Backend\Block\Widget\Button */
        $editButton = $this->getLayout()->createBlock('Magento\DesignEditor\Block\Adminhtml\Theme\Button');
        $editButton->setData(array(
            'title'  => __('Edit'),
            'label'  => __('Edit'),
            'class'  => 'action-edit',
            'href'   => $this->_getEditUrl($themeBlock->getTheme()->getId()),
            'target' => 'edittheme',
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
            'mode'     => \Magento\DesignEditor\Model\State::MODE_NAVIGATION
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
            'mode'     => \Magento\DesignEditor\Model\State::MODE_NAVIGATION
        ));
    }
}
