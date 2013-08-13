<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog bundle product info block
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Catalog_Product_View_Type_Bundle extends Magento_Catalog_Block_Product_View_Abstract
{
    protected $_optionRenderers = array();
    protected $_options         = null;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    public function getOptions()
    {
        if (!$this->_options) {
            $product = $this->getProduct();
            $typeInstance = $product->getTypeInstance();
            $typeInstance->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $typeInstance->getOptionsCollection($product);

            $selectionCollection = $typeInstance->getSelectionsCollection(
                $typeInstance->getOptionsIds($product),
                $product
            );

            $this->_options = $optionCollection->appendSelections($selectionCollection, false,
                Mage::helper('Magento_Catalog_Helper_Product')->getSkipSaleableCheck()
            );
        }

        return $this->_options;
    }

    public function hasOptions()
    {
        $this->getOptions();
        if (empty($this->_options) || !$this->getProduct()->isSalable()) {
            return false;
        }
        return true;
    }

    /**
     * Returns JSON encoded config to be used in JS scripts
     *
     * @return string
     */
    public function getJsonConfig()
    {
        Mage::app()->getLocale()->getJsPriceFormat();
        $optionsArray = $this->getOptions();
        $options      = array();
        $selected     = array();
        $currentProduct = $this->getProduct();
        /* @var $coreHelper Magento_Core_Helper_Data */
        $coreHelper   = Mage::helper('Magento_Core_Helper_Data');
        /* @var $catalogHelper Magento_Catalog_Helper_Data */
        $catalogHelper = Mage::helper('Magento_Catalog_Helper_Data');
        /* @var $taxHelper Magento_Tax_Helper_Data */
        $taxHelper = Mage::helper('Magento_Tax_Helper_Data');
        /* @var $bundlePriceModel Mage_Bundle_Model_Product_Price */
        $bundlePriceModel = Mage::getModel('Mage_Bundle_Model_Product_Price');

        if ($preConfiguredFlag = $currentProduct->hasPreconfiguredValues()) {
            $preConfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = array();
        }

        $position = 0;
        foreach ($optionsArray as $_option) {
            /* @var $_option Mage_Bundle_Model_Option */
            if (!$_option->getSelections()) {
                continue;
            }

            $optionId = $_option->getId();
            $option = array (
                'selections' => array(),
                'title'      => $_option->getTitle(),
                'isMulti'    => in_array($_option->getType(), array('multi', 'checkbox')),
                'position'   => $position++
            );

            $selectionCount = count($_option->getSelections());

            foreach ($_option->getSelections() as $_selection) {
                /* @var $_selection Magento_Catalog_Model_Product */
                $selectionId = $_selection->getSelectionId();
                $_qty = !($_selection->getSelectionQty() * 1) ? '1' : $_selection->getSelectionQty() * 1;
                // recalculate currency
                $tierPrices = $_selection->getTierPrice();
                foreach ($tierPrices as &$tierPriceInfo) {
                    $tierPriceInfo['price'] = $coreHelper->currency($tierPriceInfo['price'], false, false);
                }
                unset($tierPriceInfo); // break the reference with the last element

                $itemPrice = $bundlePriceModel->getSelectionFinalTotalPrice($currentProduct, $_selection,
                    $currentProduct->getQty(), $_selection->getQty(), false, false
                );

                $canApplyMAP = false;

                $_priceInclTax = $taxHelper->getPrice($_selection, $itemPrice, true);
                $_priceExclTax = $taxHelper->getPrice($_selection, $itemPrice);

                if ($currentProduct->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
                    $_priceInclTax = $taxHelper->getPrice($currentProduct, $itemPrice, true);
                    $_priceExclTax = $taxHelper->getPrice($currentProduct, $itemPrice);
                }

                $selection = array (
                    'qty'              => $_qty,
                    'customQty'        => $_selection->getSelectionCanChangeQty(),
                    'price'            => $coreHelper->currency($_selection->getFinalPrice(), false, false),
                    'priceInclTax'     => $coreHelper->currency($_priceInclTax, false, false),
                    'priceExclTax'     => $coreHelper->currency($_priceExclTax, false, false),
                    'priceValue'       => $coreHelper->currency($_selection->getSelectionPriceValue(), false, false),
                    'priceType'        => $_selection->getSelectionPriceType(),
                    'tierPrice'        => $tierPrices,
                    'name'             => $_selection->getName(),
                    'plusDisposition'  => 0,
                    'minusDisposition' => 0,
                    'canApplyMAP'      => $canApplyMAP
                );

                $responseObject = new Magento_Object();
                $args = array('response_object' => $responseObject, 'selection' => $_selection);
                Mage::dispatchEvent('bundle_product_view_config', $args);
                if (is_array($responseObject->getAdditionalOptions())) {
                    foreach ($responseObject->getAdditionalOptions() as $o => $v) {
                        $selection[$o] = $v;
                    }
                }
                $option['selections'][$selectionId] = $selection;

                if (($_selection->getIsDefault() || ($selectionCount == 1 && $_option->getRequired()))
                    && $_selection->isSalable()
                ) {
                    $selected[$optionId][] = $selectionId;
                }
            }
            $options[$optionId] = $option;

            // Add attribute default value (if set)
            if ($preConfiguredFlag) {
                $configValue = $preConfiguredValues->getData('bundle_option/' . $optionId);
                if ($configValue) {
                    $defaultValues[$optionId] = $configValue;
                }
            }
        }

        $config = array(
            'options'       => $options,
            'selected'      => $selected,
            'bundleId'      => $currentProduct->getId(),
            'priceFormat'   => Mage::app()->getLocale()->getJsPriceFormat(),
            'basePrice'     => $coreHelper->currency($currentProduct->getPrice(), false, false),
            'priceType'     => $currentProduct->getPriceType(),
            'specialPrice'  => $currentProduct->getSpecialPrice(),
            'includeTax'    => $taxHelper->priceIncludesTax() ? 'true' : 'false',
            'isFixedPrice'  => $this->getProduct()->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED,
            'isMAPAppliedDirectly' => $catalogHelper->canApplyMsrp($this->getProduct(), null, false)
        );

        if ($preConfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        return $coreHelper->jsonEncode($config);
    }

    public function addRenderer($type, $block)
    {
        $this->_optionRenderers[$type] = $block;
    }

    public function getOptionHtml($option)
    {
        if (!isset($this->_optionRenderers[$option->getType()])) {
            return $this->__('There is no defined renderer for "%s" option type.', $option->getType());
        }
        return $this->getLayout()->createBlock($this->_optionRenderers[$option->getType()])
            ->setOption($option)->toHtml();
    }
}
