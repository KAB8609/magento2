<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Fulltext Collection
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogSearch\Model\Resource\Fulltext;

class Collection extends \Magento\Catalog\Model\Resource\Product\Collection
{
    /**
     * Retrieve query model object
     *
     * @return \Magento\CatalogSearch\Model\Query
     */
    protected function _getQuery()
    {
        return \Mage::helper('Magento\CatalogSearch\Helper\Data')->getQuery();
    }

    /**
     * Add search query filter
     *
     * @param string $query
     * @return \Magento\CatalogSearch\Model\Resource\Fulltext\Collection
     */
    public function addSearchFilter($query)
    {
        \Mage::getSingleton('Magento\CatalogSearch\Model\Fulltext')->prepareResult();

        $this->getSelect()->joinInner(
            array('search_result' => $this->getTable('catalogsearch_result')),
            $this->getConnection()->quoteInto(
                'search_result.product_id=e.entity_id AND search_result.query_id=?',
                $this->_getQuery()->getId()
            ),
            array('relevance' => 'relevance')
        );

        return $this;
    }

    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return \Magento\CatalogSearch\Model\Resource\Fulltext\Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        if ($attribute == 'relevance') {
            $this->getSelect()->order("relevance {$dir}");
        } else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    /**
     * Stub method for campatibility with other search engines
     *
     * @return \Magento\CatalogSearch\Model\Resource\Fulltext\Collection
     */
    public function setGeneralDefaultQuery()
    {
        return $this;
    }
}
