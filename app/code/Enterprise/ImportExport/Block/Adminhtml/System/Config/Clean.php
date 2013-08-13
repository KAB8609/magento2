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
class Enterprise_ImportExport_Block_Adminhtml_System_Config_Clean extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Remove scope label
     *
     * @param  Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $url    = $this->getUrl('*/scheduled_operation/logClean', array(
            'section' => $this->getRequest()->getParam('section')
        ));
        $button = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'id'        => 'clean_now',
                'label'     => $this->helper('Enterprise_ImportExport_Helper_Data')->__('Clean Now'),
                'onclick'   => 'setLocation(\'' . $url . '\')'
            ));

        return $button->toHtml();
    }
}
