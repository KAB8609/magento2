<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reason field renderer
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_New_Tab_Items_Renderer_Reason
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->setTemplate('new/items/renderer/reason.phtml');
    }

}
