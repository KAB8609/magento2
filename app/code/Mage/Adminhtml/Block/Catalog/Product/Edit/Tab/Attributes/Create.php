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
 * New attribute panel on product edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes_Create extends Mage_Adminhtml_Block_Widget_Button
{
    /**
     * Config of create new attribute
     *
     * @var Magento_Object
     */
    protected $_config = null;

    /**
     * Retrive config of new attribute creation
     *
     * @return Magento_Object
     */
    public function getConfig()
    {
        if (is_null($this->_config)) {
           $this->_config = new Magento_Object();
        }

        return $this->_config;
    }

    protected function _beforeToHtml()
    {
        $this->setId('create_attribute_' . $this->getConfig()->getGroupId())
            ->setType('button')
            ->setClass('action-add')
            ->setLabel(Mage::helper('Mage_Adminhtml_Helper_Data')->__('New Attribute'))
            ->setDataAttribute(array('mage-init' =>
                array('productAttributes' =>
                    array(
                        'url' => $this->getUrl(
                            '*/catalog_product_attribute/new',
                            array(
                                'group' => $this->getConfig()->getAttributeGroupCode(),
                                'store' => $this->getConfig()->getStoreId(),
                                'product' => $this->getConfig()->getProductId(),
                                'type' => $this->getConfig()->getTypeId(),
                                'popup' => 1
                            )
                        )
                    )
                )
            ));

        $this->getConfig()
            ->setUrl($this->getUrl(
                '*/catalog_product_attribute/new',
                array(
                    'group' => $this->getConfig()->getAttributeGroupCode(),
                    'store' => $this->getConfig()->getStoreId(),
                    'product' => $this->getConfig()->getProductId(),
                    'type' => $this->getConfig()->getTypeId(),
                    'popup' => 1
                )
            ));

        return parent::_beforeToHtml();
    }

    protected function _toHtml()
    {
        $this->setCanShow(true);
        Mage::dispatchEvent('adminhtml_catalog_product_edit_tab_attributes_create_html_before', array('block' => $this));
        if (!$this->getCanShow()) {
            return '';
        }

        return parent::_toHtml();
    }

    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }
} // Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes_Create End
