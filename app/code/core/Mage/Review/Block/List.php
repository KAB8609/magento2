<?php
/**
 * Review list block
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Review_Block_List extends Mage_Core_Block_Template
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('review/list.phtml');
        $productId = Mage::registry('controller')->getRequest()->getParam('id', false);

        $this->_collection = Mage::getModel('review/review')->getCollection();

        $this->_collection
            ->addStoreFilter(Mage::getSingleton('core/store')->getId())
            ->addStatusFilter('approved')
            ->addEntityFilter('product', $productId)
            ->setDateOrder();

        $this->assign('reviewCount', $this->count());
        $this->assign('reviewLink', Mage::getUrl('review/product/list', array('id'=>$productId)));
    }

    public function count()
    {
        return $this->_collection->getSize();
    }

    public function toHtml()
    {
        $request    = Mage::registry('controller')->getRequest();
        $productId  = $request->getParam('id', false);

        $this->_getCollection()
            ->addRateVotes();

        $this->assign('collection', $this->_collection);

        $backUrl = Mage::getUrl('catalog/product/view/id/'.$productId);
        $this->assign('backLink', $backUrl);

        $pageUrl = clone $request;
        $this->assign('pageUrl', $pageUrl);

        return parent::toHtml();
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    protected function _initChildren()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'review_list.toolbar')
            ->setCollection($this->_getCollection());

        $this->setChild('toolbar', $toolbar);
    }

    protected function _getCollection()
    {
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_getCollection();
    }

    protected function _beforeToHtml()
    {
        $this->_getCollection()
            ->setPageSize(10)
            ->load()
            ->addRateVotes();
        return parent::_beforeToHtml();
    }
}
