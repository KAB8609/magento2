<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

// @codingStandardsIgnoreStart
/**
 * Scheduled operation create/edit form
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method string getGeneralSettingsLabel() getGeneralSettingsLabel()
 * @method string getFileSettingsLabel() getFileSettingsLabel()
 * @method string getEmailSettingsLabel() getEmailSettingsLabel()
 * @method Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
abstract class Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
    extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_ScheduledImportExport_Model_Scheduled_Operation_Data
     */
    protected $_operationData;

    /**
     * @var Magento_Backend_Model_Config_Source_Yesno
     */
    protected $_sourceYesno;

    /**
     * @var Magento_Backend_Model_Config_Source_Email_Identity
     */
    protected $_emailIdentity;

    /**
     * @var Magento_Backend_Model_Config_Source_Email_Method
     */
    protected $_emailMethod;

    /**
     * @var Magento_Core_Model_Option_ArrayPool
     */
    protected $_optionArrayPool;

    /**
     * @param Magento_Core_Model_Option_ArrayPool $optionArrayPool
     * @param Magento_Backend_Model_Config_Source_Email_Method $emailMethod
     * @param Magento_Backend_Model_Config_Source_Email_Identity $emailIdentity
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation_Data $operationData
     * @param Magento_Backend_Model_Config_Source_Yesno $sourceYesno
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Option_ArrayPool $optionArrayPool,
        Magento_Backend_Model_Config_Source_Email_Method $emailMethod,
        Magento_Backend_Model_Config_Source_Email_Identity $emailIdentity,
        Magento_ScheduledImportExport_Model_Scheduled_Operation_Data $operationData,
        Magento_Backend_Model_Config_Source_Yesno $sourceYesno,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_optionArrayPool = $optionArrayPool;
        $this->_emailMethod = $emailMethod;
        $this->_emailIdentity = $emailIdentity;
        $this->_operationData = $operationData;
        $this->_sourceYesno = $sourceYesno;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare general form for scheduled operation
     *
     * @return Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
     */
    protected function _prepareForm()
    {
        /** @var $operation Magento_ScheduledImportExport_Model_Scheduled_Operation */
        $operation = $this->_coreRegistry->registry('current_operation');
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'     => 'edit_form',
                'name'   => 'scheduled_operation',
            ))
        );
        // settings information
        $this->_addGeneralSettings($form, $operation);

        // file information
        $this->_addFileSettings($form, $operation);

        // email notifications
        $this->_addEmailSettings($form, $operation);

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setAction($this->getUrl('*/*/save'));

        $this->setForm($form);
        if (is_array($operation->getStartTime())) {
            $operation->setStartTime(join(',', $operation->getStartTime()));
        }
        $operation->setStartTime(str_replace(':', ',', $operation->getStartTime()));

        return $this;
    }

    /**
     * Add general information fieldset to form
     *
     * @param Magento_Data_Form $form
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation $operation
     * @return Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
     */
    protected function _addGeneralSettings($form, $operation)
    {
        $fieldset = $form->addFieldset('operation_settings', array(
            'legend' => $this->getGeneralSettingsLabel()
        ));

        if ($operation->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'required'  => true
            ));
        }
        $fieldset->addField('operation_type', 'hidden', array(
            'name'     => 'operation_type',
            'required' => true
        ));

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'title'     => __('Name'),
            'label'     => __('Name'),
            'required'  => true
        ));

        $fieldset->addField('details', 'textarea', array(
            'name'      => 'details',
            'title'     => __('Description'),
            'label'     => __('Description'),
            'required'  => false
        ));

        $entities = $this->_optionArrayPool->get(
            'Magento_ImportExport_Model_Source_' . uc_words($operation->getOperationType()) . '_Entity'
        )->toOptionArray();

        $fieldset->addField('entity', 'select', array(
            'name'      => 'entity_type',
            'title'     => __('Entity Type'),
            'label'     => __('Entity Type'),
            'required'  => true,
            'values'    => $entities
        ));

        $fieldset->addField('start_time', 'time', array(
            'name'      => 'start_time',
            'title'     => __('Start Time'),
            'label'     => __('Start Time'),
            'required'  => true,
        ));

        $fieldset->addField('freq', 'select', array(
            'name'      => 'freq',
            'title'     => __('Frequency'),
            'label'     => __('Frequency'),
            'required'  => true,
            'values'    => $this->_operationData->getFrequencyOptionArray()
        ));

        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'title'     => __('Status'),
            'label'     => __('Status'),
            'required'  => true,
            'values'    => $this->_operationData->getStatusesOptionArray()
        ));

        return $this;
    }

    /**
     * Add file information fieldset to form
     *
     * @param Magento_Data_Form $form
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation $operation
     * @return Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
     */
    protected function _addFileSettings($form, $operation)
    {
        $fieldset = $form->addFieldset('file_settings', array(
            'legend' => $this->getFileSettingsLabel()
        ));

        $fieldset->addField('server_type', 'select', array(
            'name'      => 'file_info[server_type]',
            'title'     => __('Server Type'),
            'label'     => __('Server Type'),
            'required'  => true,
            'values'    => $this->_operationData->getServerTypesOptionArray(),
        ));

        $fieldset->addField('file_path', 'text', array(
            'name'      => 'file_info[file_path]',
            'title'     => __('File Directory'),
            'label'     => __('File Directory'),
            'required'  => true,
            'note'      => __('For Type "Local Server" use relative path to Magento installation, e.g. var/export, var/import, var/export/some/dir')
        ));

        $fieldset->addField('host', 'text', array(
            'name'      => 'file_info[host]',
            'title'     => __('FTP Host[:Port]'),
            'label'     => __('FTP Host[:Port]'),
            'class'     => 'ftp-server server-dependent'
        ));

        $fieldset->addField('user', 'text', array(
            'name'      => 'file_info[user]',
            'title'     => __('User Name'),
            'label'     => __('User Name'),
            'class'     => 'ftp-server server-dependent'
        ));

        $fieldset->addField('password', 'password', array(
            'name'      => 'file_info[password]',
            'title'     => __('Password'),
            'label'     => __('Password'),
            'class'     => 'ftp-server server-dependent'
        ));

        $fieldset->addField('file_mode', 'select', array(
            'name'      => 'file_info[file_mode]',
            'title'     => __('File Mode'),
            'label'     => __('File Mode'),
            'values'    => $this->_operationData->getFileModesOptionArray(),
            'class'     => 'ftp-server server-dependent'
        ));

        $fieldset->addField('passive', 'select', array(
            'name'      => 'file_info[passive]',
            'title'     => __('Passive Mode'),
            'label'     => __('Passive Mode'),
            'values'    => $this->_sourceYesno->toOptionArray(),
            'class'     => 'ftp-server server-dependent'
        ));

        return $this;
    }

    /**
     * Add file information fieldset to form
     *
     * @param Magento_Data_Form $form
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation $operation
     * @return Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
     */
    protected function _addEmailSettings($form, $operation)
    {
        $fieldset = $form->addFieldSet('email_settings', array(
            'legend' => $this->getEmailSettingsLabel()
        ));

        $fieldset->addField('email_receiver', 'select', array(
            'name'      => 'email_receiver',
            'title'     => __('Failed Email Receiver'),
            'label'     => __('Failed Email Receiver'),
            'values'    => $this->_emailIdentity->toOptionArray()
        ));

        $fieldset->addField('email_sender', 'select', array(
            'name'      => 'email_sender',
            'title'     => __('Failed Email Sender'),
            'label'     => __('Failed Email Sender'),
            'values'    => $this->_emailIdentity->toOptionArray()
        ));

        $fieldset->addField('email_template', 'select', array(
            'name'      => 'email_template',
            'title'     => __('Failed Email Template'),
            'label'     => __('Failed Email Template')
        ));

        $fieldset->addField('email_copy', 'text', array(
            'name'      => 'email_copy',
            'title'     => __('Send Failed Email Copy To'),
            'label'     => __('Send Failed Email Copy To')
        ));

        $fieldset->addField('email_copy_method', 'select', array(
            'name'      => 'email_copy_method',
            'title'     => __('Send Failed Email Copy Method'),
            'label'     => __('Send Failed Email Copy Method'),
            'values'    => $this->_emailMethod->toOptionArray()
        ));

        return $this;
    }

    /**
     * Set values to form from operation model
     *
     * @param array $data
     * @return Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form|bool
     */
    protected function _setFormValues(array $data)
    {
        if (!is_object($this->getForm())) {
            return false;
        }
        if (isset($data['file_info'])) {
            $fileInfo = $data['file_info'];
            unset($data['file_info']);
            if (is_array($fileInfo)) {
                $data = array_merge($data, $fileInfo);
            }
        }
        if (isset($data['entity_type'])) {
            $data['entity'] = $data['entity_type'];
        }
        $this->getForm()->setValues($data);
        return $this;
    }
}
