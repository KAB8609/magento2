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
 * Block that renders Quick Styles > Fonts tab
 *
 * @method Mage_Core_Model_Theme getTheme()
 * @method setTheme($theme)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_QuickStyles_Fonts
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Tools_QuickStyles_AbstractTab
{
    /**
     * Tab form HTML identifier
     *
     * @var string
     */
    protected $_formId = 'quick-styles-form-fonts';

    /**
     * Controls group which will be rendered on the tab form
     *
     * @var string
     */
    protected $_tab = 'fonts';
}
