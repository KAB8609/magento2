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
 * Admin form widget
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Newsletter_Queue_Preview_Form extends Magento_Adminhtml_Block_Widget_Form
{

    /**
     * Preparing from for revision page
     *
     * @return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array(
            'id' => 'preview_form',
            'action' => $this->getUrl('*/*/drop', array('_current' => true)),
            'method' => 'post'
        ));

        if ($data = $this->getFormData()) {

            $mapper = array('preview_store_id' => 'store_id');

            foreach ($data as $key => $value) {
                if(array_key_exists($key, $mapper)) {
                    $name = $mapper[$key];
                } else {
                    $name = $key;
                }
                $form->addField($key, 'hidden', array('name' => $name));
            }
            $form->setValues($data);
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}