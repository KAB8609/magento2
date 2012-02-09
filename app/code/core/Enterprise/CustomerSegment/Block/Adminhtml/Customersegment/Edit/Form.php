<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Intialize form
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('enterprise_customersegment_segment_form');
        $this->setTitle(Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Segment Information'));
    }

    /**
     * Prepare edit form
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
