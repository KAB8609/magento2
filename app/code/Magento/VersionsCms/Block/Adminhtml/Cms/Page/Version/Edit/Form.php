<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Form for version edit page
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_VersionsCms_Block_Adminhtml_Cms_Page_Version_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    protected $_template = 'page/version/form.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $formFactory, $data);
    }

    /**
     * Preparing from for version page
     *
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Page_Revision_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = $this->_createForm(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('_current' => true)),
            'method' => 'post'
        ));

        $form->setUseContainer(true);

        /* @var $model Magento_Cms_Model_Page */
        $version = $this->_coreRegistry->registry('cms_page_version');

        $config = Mage::getSingleton('Magento_VersionsCms_Model_Config');
        /* @var $config Magento_VersionsCms_Model_Config */

        $isOwner = $config->isCurrentUserOwner($version->getUserId());
        $isPublisher = $config->canCurrentUserPublishRevision();

        $fieldset = $form->addFieldset('version_fieldset',
            array('legend' => __('Version Information'),
            'class' => 'fieldset-wide'));

        $fieldset->addField('version_id', 'hidden', array(
            'name'      => 'version_id'
        ));

        $fieldset->addField('page_id', 'hidden', array(
            'name'      => 'page_id'
        ));

        $fieldset->addField('label', 'text', array(
            'name'      => 'label',
            'label'     => __('Version Label'),
            'disabled'  => !$isOwner,
            'required'  => true
        ));

        $fieldset->addField('access_level', 'select', array(
            'label'     => __('Access Level'),
            'title'     => __('Access Level'),
            'name'      => 'access_level',
            'options'   => Mage::helper('Magento_VersionsCms_Helper_Data')->getVersionAccessLevels(),
            'disabled'  => !$isOwner && !$isPublisher
        ));

        if ($isPublisher) {
            $fieldset->addField('user_id', 'select', array(
                'label'     => __('Owner'),
                'title'     => __('Owner'),
                'name'      => 'user_id',
                'options'   => Mage::helper('Magento_VersionsCms_Helper_Data')->getUsersArray(!$version->getUserId()),
                'required'  => !$version->getUserId()
            ));
        }

        $form->setValues($version->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
