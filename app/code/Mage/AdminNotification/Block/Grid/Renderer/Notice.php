<?php
/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Block_Grid_Renderer_Notice
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        return '<span class="grid-row-title">' . $row->getTitle() . '</span>'
            . ($row->getDescription() ? '<br />' . $row->getDescription() : '');
    }
}
