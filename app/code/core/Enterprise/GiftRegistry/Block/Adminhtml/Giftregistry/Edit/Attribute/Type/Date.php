<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Type_Date
    extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('edit/type/date.phtml');
    }

    /**
     * Select element for choosing attribute type
     *
     * @return string
     */
    public function getDateFormatSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Html_Select')
            ->setData(array(
                'id'    =>  '{{prefix}}_attribute_{{id}}_date_format',
                'class' => 'select global-scope'
            ))
            ->setName('attributes[{{prefix}}][{{id}}][date_format]')
            ->setOptions($this->getDateFormatOptions());

        return $select->getHtml();
    }

    /**
     * Return array of date formats
     *
     * @return array
     */
    public function getDateFormatOptions()
    {
         return array(
            array(
                'value' => Mage_Core_Model_Locale::FORMAT_TYPE_SHORT,
                'label' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Short')
            ),
            array(
                'value' => Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
                'label' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Medium')
            ),
            array(
                'value' => Mage_Core_Model_Locale::FORMAT_TYPE_LONG,
                'label' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Long')
            ),
            array(
                'value' => Mage_Core_Model_Locale::FORMAT_TYPE_FULL,
                'label' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Full')
            )
        );
    }
}
