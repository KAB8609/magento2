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
 * Adminhtml customer orders grid action column item renderer
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Rma_Block_Adminhtml_Customer_Edit_Tab_Renderer_Action
    extends Mage_Adminhtml_Block_Sales_Reorder_Renderer_Action
{
    /**
     * Render field HRML for column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions = array();
        if ($row->getIsReturnable()) {
            $actions[] = array(
                    '@' =>  array('href' => $this->getUrl('*/rma/new', array('order_id'=>$row->getId()))),
                    '#' =>  Mage::helper('Enterprise_Rma_Helper_Data')->__('Return')
            );
        }
        $link1 = parent::render($row);
        $link2 = $this->_actionsToHtml($actions);
        $separator = $link1 && $link2 ? '<span class="separator">|</span>':'';
        return  $link1 . $separator . $link2;
    }
}
