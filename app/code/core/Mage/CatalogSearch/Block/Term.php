<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalogsearch term block
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Block_Term extends Mage_Core_Block_Template
{
    protected $_terms;
    protected $_minPopularity;
    protected $_maxPopularity;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Load terms and try to sort it by names
     *
     * @return Mage_CatalogSearch_Block_Term
     */
    protected function _loadTerms()
    {
        if (empty($this->_terms)) {
            $this->_terms = array();
            $terms = Mage::getResourceModel('Mage_CatalogSearch_Model_Resource_Query_Collection')
                ->setPopularQueryFilter(Mage::app()->getStore()->getId())
                ->setPageSize(100)
                ->load()
                ->getItems();

            if( count($terms) == 0 ) {
                return $this;
            }


            $this->_maxPopularity = reset($terms)->getPopularity();
            $this->_minPopularity = end($terms)->getPopularity();
            $range = $this->_maxPopularity - $this->_minPopularity;
            $range = ( $range == 0 ) ? 1 : $range;
            foreach ($terms as $term) {
                if( !$term->getPopularity() ) {
                    continue;
                }
                $term->setRatio(($term->getPopularity()-$this->_minPopularity)/$range);
                $temp[$term->getName()] = $term;
                $termKeys[] = $term->getName();
            }
            natcasesort($termKeys);

            foreach ($termKeys as $termKey) {
                $this->_terms[$termKey] = $temp[$termKey];
            }
        }
        return $this;
    }

    public function getTerms()
    {
        $this->_loadTerms();
        return $this->_terms;
    }

    public function getSearchUrl($obj)
    {
        $url = Mage::getModel('Mage_Core_Model_Url');
        /*
        * url encoding will be done in Url.php http_build_query
        * so no need to explicitly called urlencode for the text
        */
        $url->setQueryParam('q', $obj->getName());
        return $url->getUrl('catalogsearch/result');
    }

    public function getMaxPopularity()
    {
        return $this->_maxPopularity;
    }

    public function getMinPopularity()
    {
        return $this->_minPopularity;
    }
}
