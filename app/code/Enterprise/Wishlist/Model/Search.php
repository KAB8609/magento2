<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist search module
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Model_Search
{
    /**
     * Retrieve wishlist search results by search strategy
     *
     * @param Enterprise_Wishlist_Model_Search_Strategy_Interface $strategy
     * @return Mage_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function getResults(Enterprise_Wishlist_Model_Search_Strategy_Interface $strategy)
    {
        /* @var Mage_Wishlist_Model_Resource_Wishlist_Collection $collection */
        $collection = Mage::getModel('Mage_Wishlist_Model_Wishlist')->getCollection();
        $collection->addFieldToFilter('visibility', array('eq' => 1));
        $strategy->filterCollection($collection);
        return $collection;
    }
}
