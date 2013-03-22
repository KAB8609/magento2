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
 * Theme selectors tabs container
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_Tabs extends Mage_Backend_Block_Widget_Tabs
{
    /**
     * Initialize tab
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('theme_selector_tabs');
        $this->setDestElementId('theme_selector');
        $this->setTitle($this->__('Design & Theme Gallery'));
        $this->setIsHoriz(true);
    }

    /**
     * Add content container to template
     *
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml() . '<div id="' . $this->getDestElementId() . '" class="theme-selector"><div>' .
            $this->_getScript();
    }

    /**
     * Get additional script for tabs block
     *
     * @return string
     */
    protected function _getScript()
    {
        $script = sprintf("
            (function ($) {
                $('.themes-customizations .theme').themeControl({url: '%s'});
            })(jQuery);",
            $this->getUrl('*/*/quickEdit')
        );
        return sprintf('<script type="text/javascript">%s</script>', $script);
    }
}
