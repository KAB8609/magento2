<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product link model
 *
 * @method Mage_Catalog_Model_Resource_Product_Link _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Link getResource()
 * @method int getProductId()
 * @method Mage_Catalog_Model_Product_Link setProductId(int $value)
 * @method int getLinkedProductId()
 * @method Mage_Catalog_Model_Product_Link setLinkedProductId(int $value)
 * @method int getLinkTypeId()
 * @method Mage_Catalog_Model_Product_Link setLinkTypeId(int $value)
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Link extends Mage_Core_Model_Abstract
{
    const LINK_TYPE_RELATED     = 1;
    const LINK_TYPE_GROUPED     = 3;
    const LINK_TYPE_UPSELL      = 4;
    const LINK_TYPE_CROSSSELL   = 5;

    protected $_attributeCollection = null;

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Mage_Catalog_Model_Resource_Product_Link');
    }

    public function useRelatedLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_RELATED);
        return $this;
    }

    public function useGroupedLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_GROUPED);
        return $this;
    }

    public function useUpSellLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_UPSELL);
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product_Link
     */
    public function useCrossSellLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_CROSSSELL);
        return $this;
    }

    /**
     * Retrieve table name for attribute type
     *
     * @param   string $type
     * @return  string
     */
    public function getAttributeTypeTable($type)
    {
        return $this->_getResource()->getAttributeTypeTable($type);
    }

    /**
     * Retrieve linked product collection
     */
    public function getProductCollection()
    {
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Link_Product_Collection')
            ->setLinkModel($this);
        return $collection;
    }

    /**
     * Retrieve link collection
     */
    public function getLinkCollection()
    {
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Link_Collection')
            ->setLinkModel($this);
        return $collection;
    }

    public function getAttributes($type=null)
    {
        if (is_null($type)) {
            $type = $this->getLinkTypeId();
        }
        return $this->_getResource()->getAttributesByType($type);
    }

    /**
     * Save data for product relations
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Product_Link
     */
    public function saveProductRelations($product)
    {
        $data = $product->getRelatedLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_RELATED);
        }
        $data = $product->getUpSellLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_UPSELL);
        }
        $data = $product->getCrossSellLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_CROSSSELL);
        }
        return $this;
    }

    /**
     * Save grouped product relation links
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Link
     */
    public function saveGroupedLinks($product)
    {
        $data = $product->getGroupedLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveGroupedLinks($product, $data, self::LINK_TYPE_GROUPED);
        }
        return $this;
    }
}
