<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Rss_Block_Catalog_Abstract extends Magento_Rss_Block_Abstract
{
    /**
     * Stored price block instances
     * @var array
     */
    protected $_priceBlock = array();

    /**
     * Stored price blocks info
     * @var array
     */
    protected $_priceBlockTypes = array();

    /**
     * Default values for price block and template
     * @var string
     */
    protected $_priceBlockDefaultTemplate = 'rss/product/price.phtml';
    protected $_priceBlockDefaultType = 'Magento_Catalog_Block_Product_Price';

    /**
     * Whether to show "As low as" as a link
     * @var bool
     */
    protected $_useLinkForAsLowAs = true;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_rss';

    /**
     * Return Price Block renderer for specified product type
     *
     * @param string $productTypeId Catalog Product type
     * @return Magento_Core_Block_Abstract
     */
    protected function _getPriceBlock($productTypeId)
    {
        if (!isset($this->_priceBlock[$productTypeId])) {
            $block = $this->_priceBlockDefaultType;
            if (isset($this->_priceBlockTypes[$productTypeId])) {
                if ($this->_priceBlockTypes[$productTypeId]['block'] != '') {
                    $block = $this->_priceBlockTypes[$productTypeId]['block'];
                }
            }
            $this->_priceBlock[$productTypeId] = $this->getLayout()->createBlock($block);
        }
        return $this->_priceBlock[$productTypeId];
    }

    /**
     * Return template for Price Block renderer
     *
     * @param string $productTypeId Catalog Product type
     * @return string
     */
    protected function _getPriceBlockTemplate($productTypeId)
    {
        if (isset($this->_priceBlockTypes[$productTypeId])) {
            if ($this->_priceBlockTypes[$productTypeId]['template'] != '') {
                return $this->_priceBlockTypes[$productTypeId]['template'];
            }
        }
        return $this->_priceBlockDefaultTemplate;
    }

    /**
     * Returns product price html for RSS feed
     *
     * @param Magento_Catalog_Model_Product $product
     * @param bool $displayMinimalPrice Display "As low as" etc.
     * @param string $idSuffix Suffix for HTML containers
     * @return string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix='')
    {
        $type_id = $product->getTypeId();
        if (Mage::helper('Magento_Catalog_Helper_Data')->canApplyMsrp($product)) {
            $type_id = $this->_mapRenderer;
        }

        return $this->_getPriceBlock($type_id)
            ->setTemplate($this->_getPriceBlockTemplate($type_id))
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->setUseLinkForAsLowAs($this->_useLinkForAsLowAs)
            ->toHtml();
    }

    /**
     * Adding customized price template for product type, used as action in layouts
     *
     * @param string $type Catalog Product Type
     * @param string $block Block Type
     * @param string $template Template
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = array(
                'block' => $block,
                'template' => $template
            );
        }
    }
}