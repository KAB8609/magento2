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
 * Product form boolean field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Boolean extends Varien_Data_Form_Element_Select
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setValues(array(
            array(
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('No'),
                'value' => 0,
            ),
            array(
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Yes'),
                'value' => 1,
            ),
        ));
    }
}
