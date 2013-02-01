<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer for Invitee in invitations grid
 *
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_Grid_Column_Invitee
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render invitee email linked to its account edit page
     *
     * @param   Varien_Object $row
     * @return  string
     */
    protected function _getValue(Varien_Object $row)
    {
        if (Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Customer::manage')) {
            if (!$row->getReferralId()) {
                return '';
            }
            return '<a href="' . Mage::getSingleton('Mage_Backend_Model_Url')
                ->getUrl('*/customer/edit', array('id' => $row->getReferralId())) . '">'
                   . $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
        } else {
            return parent::_getValue($row);
        }
    }
}
