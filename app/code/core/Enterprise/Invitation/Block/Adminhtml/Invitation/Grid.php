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
 * Invitations grid
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set defaults
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('invitationGrid');
        $this->setDefaultSort('date');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Invitation_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Enterprise_Invitation_Model_Invitation')->getCollection()
            ->addWebsiteInformation()->addInviteeInformation();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Invitation_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('enterprise_invitation_id', array(
            'header'=> Mage::helper('Enterprise_Invitation_Helper_Data')->__('ID'),
            'width' => 80,
            'align' => 'right',
            'type'  => 'text',
            'index' => 'invitation_id'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Email'),
            'index' => 'invitation_email',
            'type'  => 'text'
        ));

        $renderer = (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('customer/manage'))
            ? 'Enterprise_Invitation_Block_Adminhtml_Invitation_Grid_Column_Invitee' : false;

        $this->addColumn('invitee', array(
            'header' => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitee'),
            'index'  => 'invitee_email',
            'type'   => 'text',
            'renderer' => $renderer,
        ));

        $this->addColumn('invitation_date', array(
            'header' => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Sent'),
            'index' => 'invitation_date',
            'type' => 'datetime',
            'gmtoffset' => true,
            'width' => 170
        ));

        $this->addColumn('signup_date', array(
            'header' => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Registered'),
            'index' => 'signup_date',
            'type' => 'datetime',
            'gmtoffset' => true,
            'width' => 150
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('Enterprise_Invitation_Model_Source_Invitation_Status')->getOptions(),
            'width' => 140
        ));

        $this->addColumn('website_id', array(
            'header'  => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Valid on Website'),
            'index'   => 'website_id',
            'type'    => 'options',
            'options' => Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteOptionHash(),
            'width'   => 150,
        ));

        $groups = Mage::getModel('Mage_Customer_Model_Group')->getCollection()
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group_id', array(
            'header' => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitee Group'),
            'index' => 'group_id',
            'filter_index' => 'invitee_group_id',
            'type'  => 'options',
            'options' => $groups,
            'width' => 140
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass-actions
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Invitation_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('invitation_id');
        $this->getMassactionBlock()->setFormFieldName('invitations');
        $this->getMassactionBlock()->addItem('cancel', array(
                'label' => $this->helper('Enterprise_Invitation_Helper_Data')->__('Discard Selected'),
                'url' => $this->getUrl('*/*/massCancel'),
                'confirm' => Mage::helper('Enterprise_Invitation_Helper_Data')->__('Are you sure you want to do this?')
        ));

        $this->getMassactionBlock()->addItem('resend', array(
                'label' => $this->helper('Enterprise_Invitation_Helper_Data')->__('Send Selected'),
                'url' => $this->getUrl('*/*/massResend')
        ));

        return parent::_prepareMassaction();
    }

    /**
     * Row clock callback
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }
}
