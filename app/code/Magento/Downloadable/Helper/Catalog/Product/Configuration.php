<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper for fetching properties by product configurational item
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Helper_Catalog_Product_Configuration extends Magento_Core_Helper_Abstract
    implements Magento_Catalog_Helper_Product_Configuration_Interface
{
    /**
     * Catalog product configuration
     *
     * @var Magento_Catalog_Helper_Product_Configuration
     */
    protected $_productConfigur = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Catalog_Helper_Product_Configuration $productConfigur
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Configuration $productConfigur,
        Magento_Core_Helper_Context $context,
    Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_productConfigur = $productConfigur;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Retrieves item links options
     *
     * @param Magento_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getLinks(Magento_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $product = $item->getProduct();
        $itemLinks = array();
        $linkIds = $item->getOptionByCode('downloadable_link_ids');
        if ($linkIds) {
            $productLinks = $product->getTypeInstance()
                ->getLinks($product);
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($productLinks[$linkId])) {
                    $itemLinks[] = $productLinks[$linkId];
                }
            }
        }
        return $itemLinks;
    }

    /**
     * Retrieves product links section title
     *
     * @param Magento_Catalog_Model_Product $product
     * @return string
     */
    public function getLinksTitle($product)
    {
        $title = $product->getLinksTitle();
        if (strlen($title)) {
            return $title;
        }
        return $this->_coreStoreConfig->getConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Retrieves product options
     *
     * @param Magento_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getOptions(Magento_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $options = $this->_productConfigur->getOptions($item);

        $links = $this->getLinks($item);
        if ($links) {
            $linksOption = array(
                'label' => $this->getLinksTitle($item->getProduct()),
                'value' => array()
            );
            foreach ($links as $link) {
                $linksOption['value'][] = $link->getTitle();
            }
            $options[] = $linksOption;
        }

        return $options;
    }
}
