<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Tax Rule Edit Form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Checkout_Agreement_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('checkoutAgreementForm');
        $this->setTitle(Mage::helper('checkout')->__('Terms and Conditions Information'));
    }

    /**
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model  = Mage::registry('checkout_agreement');
        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Terms and Conditions Information')
        ));

        if ($model->getId()) {
        	$fieldset->addField('agreement_id', 'hidden', array(
                'name' => 'agreement_id',
            ));
        }
    	$fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('checkout')->__('Condition Name'),
            'title'     => Mage::helper('checkout')->__('Condition Name'),
            'required'  => true,
        ));

    	$fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('checkout')->__('Status'),
            'title'     => Mage::helper('checkout')->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('checkout')->__('Enabled'),
                '0' => Mage::helper('checkout')->__('Disabled'),
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
        	$fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('checkout')->__('Store View'),
                'title'     => Mage::helper('checkout')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('checkbox_text', 'editor', array(
            'name'      => 'checkbox_text',
            'label'     => Mage::helper('checkout')->__('Checkbox text'),
            'title'     => Mage::helper('checkout')->__('Checkbox text'),
            'wysiwyg'   => false,
            'required'  => true,
        ));

    	$fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('checkout')->__('Content'),
            'title'     => Mage::helper('checkout')->__('Content'),
            'style'     => 'width: 98%; height: 600px;',
            'wysiwyg'   => false,
            'required'  => true,
        ));

        $fieldset->addField('content_height', 'text', array(
            'name'      => 'content_height',
            'label'     => Mage::helper('checkout')->__('Content Height (css)'),
            'title'     => Mage::helper('checkout')->__('Content Height'),
            'maxlength' => 25,
            'class'     => 'validate-css-length',
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
