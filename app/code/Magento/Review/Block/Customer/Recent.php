<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recent Customer Reviews Block
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Review_Block_Customer_Recent extends Magento_Core_Block_Template
{
    protected $_template = 'customer/list.phtml';

    /**
     * Product reviews collection
     *
     * @var Magento_Review_Model_Resource_Review_Product_Collection
     */
    protected $_collection;

    protected function _initCollection()
    {
        $this->_collection = Mage::getModel('Magento_Review_Model_Review')->getProductCollection();
        $this->_collection
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addCustomerFilter(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId())
            ->setDateOrder()
            ->setPageSize(5)
            ->load()
            ->addReviewSummary();
        return $this;
    }

    public function count()
    {
        return $this->_getCollection()->getSize();
    }

    protected function _getCollection()
    {
        if (!$this->_collection) {
            $this->_initCollection();
        }
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_getCollection();
    }

    public function getReviewLink()
    {
        return Mage::getUrl('review/customer/view/');
    }

    public function getProductLink()
    {
        return Mage::getUrl('catalog/product/view/');
    }

    public function dateFormat($date)
    {
        return $this->formatDate($date, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
    }

    public function getAllReviewsUrl()
    {
        return Mage::getUrl('review/customer');
    }

    public function getReviewUrl($id)
    {
        return Mage::getUrl('review/customer/view', array('id' => $id));
    }
}