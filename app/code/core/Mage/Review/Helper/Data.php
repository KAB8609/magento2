<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default review helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_REVIEW_GUETS_ALLOW = 'catalog/review/allow_guest';

    public function getDetail($origDetail){
        return nl2br(Mage::helper('Mage_Core_Helper_String')->truncate($origDetail, 50));
    }

    /**
     * getDetailHtml return short detail info in HTML
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail){
        return nl2br(Mage::helper('Mage_Core_Helper_String')->truncate($this->escapeHtml($origDetail), 50));
    }

    public function getIsGuestAllowToWrite()
    {
        return Mage::getStoreConfigFlag(self::XML_REVIEW_GUETS_ALLOW);
    }

    /**
     * Get review statuses with their codes
     *
     * @return array
     */
    public function getReviewStatuses()
    {
        return array(
            Mage_Review_Model_Review::STATUS_APPROVED     => $this->__('Approved'),
            Mage_Review_Model_Review::STATUS_PENDING      => $this->__('Pending'),
            Mage_Review_Model_Review::STATUS_NOT_APPROVED => $this->__('Not Approved'),
        );
    }

    /**
     * Get review statuses as option array
     *
     * @return array
     */
    public function getReviewStatusesOptionArray()
    {
        $result = array();
        foreach ($this->getReviewStatuses() as $k => $v) {
            $result[] = array('value' => $k, 'label' => $v);
        }

        return $result;
    }
}
