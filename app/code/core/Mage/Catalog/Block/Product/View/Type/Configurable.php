<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog super product configurable part block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Abstract
{
    /**
     * Retrive product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = parent::getProduct();
        if (is_null($product->getTypeInstance()->getStoreFilter())) {
            $product->getTypeInstance()->setStoreFilter(Mage::app()->getStore());
        }

        return $product;
    }

    public function getAllowAttributes()
    {
        return $this->getProduct()->getTypeInstance()->getConfigurableAttributes();
    }

    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $allProducts = $this->getProduct()->getTypeInstance()->getUsedProducts();
            foreach ($allProducts as $product) {
            	if ($product->isSaleable()) {
            	    $products[] = $product;
            	}
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }

    public function getJsonConfig()
    {
        $attributes = array();
        $options = array();
        $store = Mage::app()->getStore();

        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();

            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttribute->getId()])) {
                    $options[$productAttribute->getId()] = array();
                }

                if (!isset($options[$productAttribute->getId()][$attributeValue])) {
                    $options[$productAttribute->getId()][$attributeValue] = array();
                }
                $options[$productAttribute->getId()][$attributeValue][] = $productId;
            }
        }

        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
               'id'        => $productAttribute->getId(),
               'code'      => $productAttribute->getAttributeCode(),
               'label'     => $attribute->getLabel(),
               'options'   => array()
            );

            foreach ($attribute->getPrices() as $value) {
                if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
                    continue;
                }

                $info['options'][] = array(
                    'id'    => $value['value_index'],
                    'label' => $value['label'],
                    'price' => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                    'products'   => isset($options[$attributeId][$value['value_index']]) ? $options[$attributeId][$value['value_index']] : array(),
                );
            }

            if($this->_validateAttributeInfo($info)) {
               $attributes[$attributeId] = $info;
            }
        }

        $config = array(
            'attributes'=> $attributes,
            'template'  => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice' => $this->_preparePrice($this->getProduct()->getFinalPrice()),
            'productId' => $this->getProduct()->getId(),
            'chooseText'=> Mage::helper('catalog')->__('Choose option...'),
        );

        return Zend_Json::encode($config);
    }

    /**
     * Validating of super product option value
     *
     * @param array $attribute
     * @param array $value
     * @param array $options
     * @return boolean
     */
    protected function _validateAttributeValue($attributeId, &$value, &$options)
    {
        if(isset($options[$attributeId][$value['value_index']])) {
            return true;
        }

        return false;
    }

    /**
     * Validation of super product option
     *
     * @param array $info
     * @return boolean
     */
    protected function _validateAttributeInfo(&$info)
    {
        if(count($info['options']) > 0) {
            return true;
        }
        return false;
    }

    protected function _preparePrice($price, $isPercent=false)
    {
        try {
            if ($isPercent) {
                $price = $this->getProduct()->getFinalPrice()*$price/100;
            }
            $price = Mage::app()->getStore()->convertPrice($price);
            $price = Zend_Locale_Format::toNumber($price, array('number_format'=>'##0.00'));
            return str_replace(',', '.', $price);
        } catch (Exception $e) {
            $price = Zend_Locale_Format::toNumber(0, array('number_format'=>'##0.00'));
            return str_replace(',', '.', $price);
        }
    }
}