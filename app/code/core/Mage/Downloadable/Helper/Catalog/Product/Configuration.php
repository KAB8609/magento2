<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper for fetching properties by product configurational item
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Helper_Catalog_Product_Configuration extends Mage_Core_Helper_Abstract
    implements Mage_Catalog_Helper_Product_Configuration_Interface
{
    /**
     * Retrieves item links options
     *
     * @param Mage_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getLinks(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
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
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getLinksTitle($product)
    {
        $title = $product->getLinksTitle();
        if (strlen($title)) {
            return $title;
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Retrieves product options
     *
     * @param Mage_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $options = Mage::helper('Mage_Catalog_Helper_Product_Configuration')->getOptions($item);

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
