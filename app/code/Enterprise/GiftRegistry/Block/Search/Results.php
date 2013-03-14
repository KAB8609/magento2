<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry search results
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_Block_Search_Results extends Mage_Core_Block_Template
{
    /**
     * Set search results and create html pager block
     */
    public function setSearchResults($results)
    {
        $this->setData('search_results', $results);
        $pager = $this->getLayout()->createBlock('Mage_Page_Block_Html_Pager', 'giftregistry.search.pager')
            ->setCollection($results)->setIsOutputRequired(false);
        $this->setChild('pager', $pager);
    }

    /**
     * Return frontend registry link
     *
     * @param Enterprise_GiftRegistry_Model_Entity $item
     * @return string
     */
    public function getRegistryLink($item)
    {
        return $this->getUrl('*/view/index', array('id' => $item->getUrlKey()));
    }

    /**
     * Retrieve item formated date
     *
     * @param Enterprise_GiftRegistry_Model_Entity $item
     * @return string
     */
    public function getFormattedDate($item)
    {
        if ($item->getEventDate()) {
            return $this->formatDate($item->getEventDate(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        }
    }
}
