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
 * Invitation view block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_View extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Set header text, add some buttons
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Invitation_View
     */
    protected function _prepareLayout()
    {
        $invitation = $this->getInvitation();
        $this->_headerText = Mage::helper('enterprise_invitation')->__('View Invitation for %s (ID: %s)', $invitation->getEmail(), $invitation->getId());
        $this->_addButton('back', array(
            'label' => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Back'),
            'onclick' => "setLocation('{$this->getUrl('*/*/')}')",
            'class' => 'back',
        ), -1);
        if ($invitation->canBeCanceled()) {
            $massCancelUrl = $this->getUrl('*/*/massCancel', array('_query' => array('invitations' => array($invitation->getId()))));
            $this->_addButton('cancel', array(
                'label' => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Discard Invitation'),
                'onclick' => 'deleteConfirm(\''. $this->jsQuoteEscape(
                            Mage::helper('Enterprise_Invitation_Helper_Data')->__('Are you sure you want to discard this invitation?')
                        ) . '\', \'' . $massCancelUrl . '\' )',
                'class' => 'cancel'
            ), -1);
        }
        if ($invitation->canMessageBeUpdated()) {
            $this->_addButton('save_message_button', array(
                'label'   => $this->helper('Enterprise_Invitation_Helper_Data')->__('Save Invitation'),
                'onclick' => 'invitationForm.submit()',
            ), -1);
        }
        if ($invitation->canBeSent()) {
            $massResendUrl = $this->getUrl('*/*/massResend', array('_query' => http_build_query(array('invitations' => array($invitation->getId())))));
            $this->_addButton('resend', array(
                'label' => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Send Invitation'),
                'onclick' => "setLocation('{$massResendUrl}')",
            ), -1);
        }

        parent::_prepareLayout();
    }

    /**
     * Return Invitation for view
     *
     * @return Enterprise_Invitation_Model_Invitation
     */
    public function getInvitation()
    {
        return Mage::registry('current_invitation');
    }

    /**
     * Retrieve save message url
     *
     * @return string
     */
    public function getSaveMessageUrl()
    {
        return $this->getUrl('*/*/saveInvitation', array('id'=>$this->getInvitation()->getId()));
    }
}