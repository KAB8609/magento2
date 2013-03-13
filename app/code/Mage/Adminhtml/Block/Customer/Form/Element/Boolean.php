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
 * Customer Widget Form Boolean Element Block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Form_Element_Boolean extends Varien_Data_Form_Element_Select
{
    /**
     * Prepare default SELECT values
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setValues(array(
            array(
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('No'),
                'value' => '0',
            ),
            array(
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Yes'),
                'value' => 1,
            )
        ));
    }
}
