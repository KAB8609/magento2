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
 * customers defined options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract extends Mage_Adminhtml_Block_Widget
{
    protected $_name = 'abstract';

    protected function _prepareLayout()
    {
        $this->setChild('option_price_type',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Html_Select')
                ->setData(array(
                    'id' => 'product_option_{{option_id}}_price_type',
                    'class' => 'select product-option-price-type'
                ))
        );

        $this->getChildBlock('option_price_type')->setName('product[options][{{option_id}}][price_type]')
            ->setOptions(Mage::getSingleton('Mage_Adminhtml_Model_System_Config_Source_Product_Options_Price')
            ->toOptionArray());

        return parent::_prepareLayout();
    }

    /**
     * Get html of Price Type select element
     *
     * @return string
     */
    public function getPriceTypeSelectHtml()
    {
        if ($this->getCanEditPrice() === false) {
            $this->getChildBlock('option_price_type')->setExtraParams('disabled="disabled"');
        }
        return $this->getChildHtml('option_price_type');
    }

}
