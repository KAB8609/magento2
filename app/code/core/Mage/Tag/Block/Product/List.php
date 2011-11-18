<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tag_Block_Product_List extends Mage_Core_Block_Template
{
    protected $_collection;

    /**
     * Unique Html Id
     *
     * @var string
     */
    protected $_uniqueHtmlId = null;

    public function getCount()
    {
        return count($this->getTags());
    }

    public function getTags()
    {
        return $this->_getCollection()->getItems();
    }

    public function getProductId()
    {
        if ($product = Mage::registry('current_product')) {
            return $product->getId();
        }
        return false;
    }

    protected function _getCollection()
    {
        if( !$this->_collection && $this->getProductId() ) {

            $model = Mage::getModel('Mage_Tag_Model_Tag');
            $this->_collection = $model->getResourceCollection()
                ->addPopularity()
                ->addStatusFilter($model->getApprovedStatus())
                ->addProductFilter($this->getProductId())
                ->setFlag('relation', true)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->setActiveFilter()
                ->load();
        }
        return $this->_collection;
    }

    protected function _beforeToHtml()
    {
        if (!$this->getProductId()) {
            return false;
        }

        return parent::_beforeToHtml();
    }

    public function getFormAction()
    {
        $helper = Mage::helper('Mage_Core_Helper_Url');
        return Mage::getUrl('tag/index/save', array(
            'product' => $this->getProductId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $helper->getEncodedUrl()
        ));
    }

    /**
     * Render tags by specified pattern and implode them by specified 'glue' string
     *
     * @param string $pattern
     * @param string $glue
     * @return string
     */
    public function renderTags($pattern, $glue = ' ')
    {
        $out = array();
        foreach ($this->getTags() as $tag) {
            $out[] = sprintf($pattern,
                $tag->getTaggedProductsUrl(), $this->escapeHtml($tag->getName()), $tag->getProducts()
            );
        }
        return implode($out, $glue);
    }

    /**
     * Generate unique html id
     *
     * @param string $prefix
     * @return string
     */
    public function getUniqueHtmlId($prefix = '')
    {
        if (is_null($this->_uniqueHtmlId)) {
            $this->_uniqueHtmlId = Mage::helper('Mage_Core_Helper_Data')->uniqHash($prefix);
        }
        return $this->_uniqueHtmlId;
    }
}
