<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml edit admin user account form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\System\Account\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_userFactory = $userFactory;
        $this->_authSession = $authSession;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    protected function _prepareForm()
    {
        $userId = $this->_authSession->getUser()->getId();
        $user = $this->_userFactory->create()->load($userId);
        $user->unsetData('password');

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account Information')));

        $fieldset->addField('username', 'text', array(
            'name'  => 'username',
            'label' => __('User Name'),
            'title' => __('User Name'),
            'required' => true,
        ));

        $fieldset->addField('firstname', 'text', array(
            'name'  => 'firstname',
            'label' => __('First Name'),
            'title' => __('First Name'),
            'required' => true,
        ));

        $fieldset->addField('lastname', 'text', array(
            'name'  => 'lastname',
            'label' => __('Last Name'),
            'title' => __('Last Name'),
            'required' => true,
        ));

        $fieldset->addField('user_id', 'hidden', array(
            'name'  => 'user_id',
        ));

        $fieldset->addField('email', 'text', array(
            'name'  => 'email',
            'label' => __('Email'),
            'title' => __('User Email'),
            'required' => true,
        ));

        $fieldset->addField('password', 'password', array(
            'name'  => 'password',
            'label' => __('New Password'),
            'title' => __('New Password'),
            'class' => 'input-text validate-admin-password',
        ));

        $fieldset->addField('confirmation', 'password', array(
            'name'  => 'password_confirmation',
            'label' => __('Password Confirmation'),
            'class' => 'input-text validate-cpassword',
        ));

        $fieldset->addField('interface_locale', 'select', array(
            'name'   => 'interface_locale',
            'label'  => __('Interface Locale'),
            'title'  => __('Interface Locale'),
            'values' => $this->_locale->getTranslatedOptionLocales(),
            'class'  => 'select',
        ));

        $form->setValues($user->getData());
        $form->setAction($this->getUrl('adminhtml/system_account/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
