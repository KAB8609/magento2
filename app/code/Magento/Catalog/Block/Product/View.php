<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product View block
 *
 * @category Magento
 * @package  Magento_Catalog
 * @module   Catalog
 * @author   Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Product;

class View extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Add meta information from product to head block
     *
     * @return \Magento\Catalog\Block\Product\View
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('Magento\Catalog\Block\Breadcrumbs');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $product = $this->getProduct();
            $title = $product->getMetaTitle();
            if ($title) {
                $headBlock->setTitle($title);
            }
            $keyword = $product->getMetaKeyword();
            $currentCategory = \Mage::registry('current_category');
            if ($keyword) {
                $headBlock->setKeywords($keyword);
            } elseif($currentCategory) {
                $headBlock->setKeywords($product->getName());
            }
            $description = $product->getMetaDescription();
            if ($description) {
                $headBlock->setDescription( ($description) );
            } else {
                $headBlock->setDescription(\Mage::helper('Magento\Core\Helper\String')->substr($product->getDescription(), 0, 255));
            }
            if ($this->helper('Magento\Catalog\Helper\Product')->canUseCanonicalTag()) {
                $params = array('_ignore_category'=>true);
                $headBlock->addLinkRel('canonical', $product->getUrlModel()->getUrl($product, $params));
            }
        }
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->getProduct()->getName());
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!\Mage::registry('product') && $this->getProductId()) {
            $product = \Mage::getModel('Magento\Catalog\Model\Product')->load($this->getProductId());
            \Mage::register('product', $product);
        }
        return \Mage::registry('product');
    }

    /**
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend()
    {
        $sendToFriendModel = \Mage::registry('send_to_friend_model');
        return $sendToFriendModel && $sendToFriendModel->canEmailToFriend();
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = \Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = \Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        $additional[$addUrlKey] = \Mage::helper('Magento\Core\Helper\Data')->urlEncode($addUrlValue);

        return $this->helper('Magento\Checkout\Helper\Cart')->getAddUrl($product, $additional);
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();
        if (!$this->hasOptions()) {
            return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($config);
        }

        $_request = \Mage::getSingleton('Magento\Tax\Model\Calculation')->getRateRequest(false, false, false);
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->getProduct();
        $_request->setProductClassId($product->getTaxClassId());
        $defaultTax = \Mage::getSingleton('Magento\Tax\Model\Calculation')->getRate($_request);

        $_request = \Mage::getSingleton('Magento\Tax\Model\Calculation')->getRateRequest();
        $_request->setProductClassId($product->getTaxClassId());
        $currentTax = \Mage::getSingleton('Magento\Tax\Model\Calculation')->getRate($_request);

        $_regularPrice = $product->getPrice();
        $_finalPrice = $product->getFinalPrice();
        $_priceInclTax = \Mage::helper('Magento\Tax\Helper\Data')->getPrice($product, $_finalPrice, true);
        $_priceExclTax = \Mage::helper('Magento\Tax\Helper\Data')->getPrice($product, $_finalPrice);
        $_tierPrices = array();
        $_tierPricesInclTax = array();
        foreach ($product->getTierPrice() as $tierPrice) {
            $_tierPrices[] = \Mage::helper('Magento\Core\Helper\Data')->currency($tierPrice['website_price'], false, false);
            $_tierPricesInclTax[] = \Mage::helper('Magento\Core\Helper\Data')->currency(
                \Mage::helper('Magento\Tax\Helper\Data')->getPrice($product, (int)$tierPrice['website_price'], true),
                false, false);
        }
        $config = array(
            'productId'           => $product->getId(),
            'priceFormat'         => \Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'          => \Mage::helper('Magento\Tax\Helper\Data')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'      => \Mage::helper('Magento\Tax\Helper\Data')->displayPriceIncludingTax(),
            'showBothPrices'      => \Mage::helper('Magento\Tax\Helper\Data')->displayBothPrices(),
            'productPrice'        => \Mage::helper('Magento\Core\Helper\Data')->currency($_finalPrice, false, false),
            'productOldPrice'     => \Mage::helper('Magento\Core\Helper\Data')->currency($_regularPrice, false, false),
            'priceInclTax'        => \Mage::helper('Magento\Core\Helper\Data')->currency($_priceInclTax, false, false),
            'priceExclTax'        => \Mage::helper('Magento\Core\Helper\Data')->currency($_priceExclTax, false, false),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'idSuffix'            => '_clone',
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'plusDispositionTax'  => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
            'tierPrices'          => $_tierPrices,
            'tierPricesInclTax'   => $_tierPricesInclTax,
        );

        $responseObject = new \Magento\Object();
        \Mage::dispatchEvent('catalog_product_view_config', array('response_object'=>$responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option=>$value) {
                $config[$option] = $value;
            }
        }

        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($config);
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getTypeInstance()->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }

    /**
     * Check if product has required options
     *
     * @return bool
     */
    public function hasRequiredOptions()
    {
        return $this->getProduct()->getTypeInstance()->hasRequiredOptions($this->getProduct());
    }

    /**
     * Define if setting of product options must be shown instantly.
     * Used in case when options are usually hidden and shown only when user
     * presses some button or link. In editing mode we better show these options
     * instantly.
     *
     * @return bool
     */
    public function isStartCustomization()
    {
        return $this->getProduct()->getConfigureMode() || \Mage::app()->getRequest()->getParam('startcustomization');
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param null|\Magento\Catalog\Model\Product $product
     * @return int|float
     */
    public function getProductDefaultQty($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }

        return $qty;
    }

    /**
     * Get container name, where product options should be displayed
     *
     * @return string
     */
    public function getOptionsContainer()
    {
        return $this->getProduct()->getOptionsContainer() == 'container1' ? 'container1' : 'container2';
    }
}
