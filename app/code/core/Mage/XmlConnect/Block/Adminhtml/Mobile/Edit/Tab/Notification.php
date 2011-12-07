<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tab for Notifications Management
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Notification
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Constructor
     * Setting view options
     */
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Prepare form before rendering HTML
     * Setting Form Fieldsets and fields
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $this->setForm($form);

        $data = Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication()->getFormData();

        $yesNoValues = Mage::getModel('Mage_Adminhtml_Model_System_Config_Source_Yesno')->toOptionArray();

        $fieldset = $form->addFieldset('notifications', array(
            'legend'    => $this->__('Urban Airship Push Notification'),
        ));

        if (isset($data['conf[native][notifications][isActive]'])) {
            $notificationStatus = $data['conf[native][notifications][isActive]'];
        } else {
            $notificationStatus = '0';
        }

        $notificationEnabled = $fieldset->addField('conf/native/notifisations/isActive', 'select', array(
            'label'     => $this->__('Enable AirMail Message Push notification'),
            'name'      => 'conf[native][notifications][isActive]',
            'values'    => $yesNoValues,
            'value'     => $notificationStatus,
        ));

        if (isset($data['conf[native][notifications][applicationKey]'])) {
            $keyValue = $data['conf[native][notifications][applicationKey]'];
        } else {
            $keyValue = '';
        }

        $applicationKey = $fieldset->addField('conf/native/notifications/applicationKey', 'text', array(
            'label'     => $this->__('Application Key'),
            'name'      => 'conf[native][notifications][applicationKey]',
            'value'     => $keyValue,
            'required'  => true
        ));

        if (isset($data['conf[native][notifications][applicationSecret]'])) {
            $secretValue = $data['conf[native][notifications][applicationSecret]'];
        } else {
            $secretValue = '';
        }

        $applicationSecret = $fieldset->addField('conf/native/notifications/applicationSecret', 'text', array(
            'label'     => $this->__('Application Secret'),
            'name'      => 'conf[native][notifications][applicationSecret]',
            'value'     => $secretValue,
            'required'  => true
        ));

        if (isset($data['conf[native][notifications][applicationMasterSecret]'])) {
            $mSecretValue = $data['conf[native][notifications][applicationMasterSecret]'];
        } else {
            $mSecretValue = '';
        }

        $mSecretConfPath = 'conf/native/notifications/applicationMasterSecret';
        $applicationMasterSecret = $fieldset->addField($mSecretConfPath, 'text', array(
            'label'     => $this->__('Application Master Secret'),
            'name'      => 'conf[native][notifications][applicationMasterSecret]',
            'value'     => $mSecretValue,
            'required'  => true
        ));

        if (isset($data['conf[native][notifications][mailboxTitle]'])) {
            $titleValue = $data['conf[native][notifications][mailboxTitle]'];
        } else {
            $titleValue = '';
        }

        $mailboxTitle = $fieldset->addField('conf/native/notifications/mailboxTitle', 'text', array(
            'label'     => $this->__('Mailbox title'),
            'name'      => 'conf[native][notifications][mailboxTitle]',
            'value'     => $titleValue,
            'required'  => true,
            'note'      => $this->__('The Mailbox title will be shown in the More Info tab. To understand more about the title, please <a target="_blank" href="http://www.magentocommerce.com/img/product/mobile/helpers/mail_box_title.png">click here</a>')
        ));

        // field dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Form_Element_Dependence')
                ->addFieldMap($applicationKey->getHtmlId(), $applicationKey->getName())
                ->addFieldMap($applicationSecret->getHtmlId(), $applicationSecret->getName())
                ->addFieldMap($applicationMasterSecret->getHtmlId(), $applicationMasterSecret->getName())
                ->addFieldMap($mailboxTitle->getHtmlId(), $mailboxTitle->getName())
                ->addFieldMap($notificationEnabled->getHtmlId(), $notificationEnabled->getName())
                ->addFieldDependence(
                    $applicationKey->getName(),
                    $notificationEnabled->getName(),
                    1)
                ->addFieldDependence(
                    $applicationSecret->getName(),
                    $notificationEnabled->getName(),
                    1)
                ->addFieldDependence(
                    $applicationMasterSecret->getName(),
                    $notificationEnabled->getName(),
                    1)
                ->addFieldDependence(
                    $mailboxTitle->getName(),
                    $notificationEnabled->getName(),
                    1)
            );
        return parent::_prepareForm();
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Push Notification');
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Push Notification');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        return (bool) !Mage::getSingleton('Mage_Adminhtml_Model_Session')->getNewApplication()
            && Mage::helper('Mage_XmlConnect_Helper_Data')->isNotificationsAllowed();
    }

    /**
     * Check if tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        if (!$this->getData('conf/special/notifications_submitted')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Append helper above form
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getChildHtml('app_notification_helper') . parent::_toHtml();
    }
}
