<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Clean now import/export file history button renderer
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Block_Adminhtml_System_Config_Clean extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Remove scope label
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $url    = $this->getUrl('*/scheduled_operation/logClean', array(
            'section' => $this->getRequest()->getParam('section')
        ));
        $button = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'id'        => 'clean_now',
                'label'     => $this->helper('Enterprise_ImportExport_Helper_Data')->__('Clean Now'),
                'onclick'   => 'setLocation(\'' . $url . '\')'
            ));

        return $button->toHtml();
    }
}
