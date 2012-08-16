<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml summary rating stars
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Review_Rating_Summary extends Mage_Adminhtml_Block_Template
{
    protected $_template = 'rating/stars/summary.phtml';

    protected function _construct()
    {
        if (Mage::registry('review_data')) {
            $this->setReviewId(Mage::registry('review_data')->getId());
        }
    }

    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            $ratingCollection = Mage::getModel('Mage_Rating_Model_Rating_Option_Vote')
                ->getResourceCollection()
                ->setReviewFilter($this->getReviewId())
                ->addRatingInfo()
                ->load();
            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }
        return $this->getRatingCollection();
    }

    public function getRatingSummary()
    {
        if( !$this->getRatingSummaryCache() ) {
            $this->setRatingSummaryCache(Mage::getModel('Mage_Rating_Model_Rating')->getReviewSummary($this->getReviewId()));
        }

        return $this->getRatingSummaryCache();
    }
}
