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
 * Theme selector tab for customized themes
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_Tab_Customizations
    extends Mage_DesignEditor_Block_Adminhtml_Theme_Selector_TabAbstract
{
    /**
     * Initialize tab block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setActive(true);
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('My Customizations');
    }
}
