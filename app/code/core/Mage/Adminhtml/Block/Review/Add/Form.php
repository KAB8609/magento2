<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml add product review form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Review_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('add_review_form', array('legend' => Mage::helper('Mage_Review_Helper_Data')->__('Review Details')));

        $fieldset->addField('product_name', 'note', array(
            'label'     => Mage::helper('Mage_Review_Helper_Data')->__('Product'),
            'text'      => 'product_name',
        ));

        $fieldset->addField('detailed_rating', 'note', array(
            'label'     => Mage::helper('Mage_Review_Helper_Data')->__('Product Rating'),
            'required'  => true,
            'text'      => '<div id="rating_detail">'
                . $this->getLayout()->createBlock('Mage_Adminhtml_Block_Review_Rating_Detailed')->toHtml() . '</div>',
        ));

        $fieldset->addField('status_id', 'select', array(
            'label'     => Mage::helper('Mage_Review_Helper_Data')->__('Status'),
            'required'  => true,
            'name'      => 'status_id',
            'values'    => Mage::helper('review')->getReviewStatusesOptionArray(),
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('select_stores', 'multiselect', array(
                'label'     => Mage::helper('Mage_Review_Helper_Data')->__('Visible In'),
                'required'  => true,
                'name'      => 'select_stores[]',
                'values'    => Mage::getSingleton('Mage_Adminhtml_Model_System_Store')->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('nickname', 'text', array(
            'name'      => 'nickname',
            'title'     => Mage::helper('Mage_Review_Helper_Data')->__('Nickname'),
            'label'     => Mage::helper('Mage_Review_Helper_Data')->__('Nickname'),
            'maxlength' => '50',
            'required'  => true,
        ));

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'title'     => Mage::helper('Mage_Review_Helper_Data')->__('Summary of Review'),
            'label'     => Mage::helper('Mage_Review_Helper_Data')->__('Summary of Review'),
            'maxlength' => '255',
            'required'  => true,
        ));

        $fieldset->addField('detail', 'textarea', array(
            'name'      => 'detail',
            'title'     => Mage::helper('Mage_Review_Helper_Data')->__('Review'),
            'label'     => Mage::helper('Mage_Review_Helper_Data')->__('Review'),
            'style'     => 'height: 600px;',
            'required'  => true,
        ));

        $fieldset->addField('product_id', 'hidden', array(
            'name'      => 'product_id',
        ));

        /*$gridFieldset = $form->addFieldset('add_review_grid', array('legend' => Mage::helper('Mage_Review_Helper_Data')->__('Please select a product')));
        $gridFieldset->addField('products_grid', 'note', array(
            'text' => $this->getLayout()->createBlock('Mage_Adminhtml_Block_Review_Product_Grid')->toHtml(),
        ));*/

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/post'));

        $this->setForm($form);
    }
}
