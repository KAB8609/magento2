<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Review extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Limit to product review text length
     */
    const REVIEW_DETAIL_TRUNCATE_LEN = 200;

    /**
     * Retrieve review data as xml object
     *
     * @param Mage_Review_Model_Review $review
     * @param string $itemNodeName
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function reviewToXmlObject(Mage_Review_Model_Review $review, $itemNodeName = 'item')
    {
        $rating = 0;
        $item = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<' . $itemNodeName . '></' . $itemNodeName . '>'));
        if ($review->getId()) {
            $item->addChild('review_id', $review->getId());
            $item->addChild('created_at', $this->formatDate($review->getCreatedAt()));
            $item->addChild('title', $item->escapeXml($review->getTitle()));
            $item->addChild('nickname', $item->escapeXml($review->getNickname()));
            $detail = $item->escapeXml($review->getDetail());
            if ($itemNodeName == 'item') {
                $remainder = '';
                $deviceType = Mage::helper('Mage_XmlConnect_Helper_Data')->getDeviceType();
                if ($deviceType != Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD) {
                    $detail = Mage::helper('Mage_Core_Helper_String')
                        ->truncate($detail, self::REVIEW_DETAIL_TRUNCATE_LEN, '', $remainder, false);
                }
            }
            $item->addChild('detail', $detail);

            $summary = Mage::getModel('Mage_Rating_Model_Rating')->getReviewSummary($review->getId());
            if ($summary->getCount() > 0) {
                $rating = round($summary->getSum() / $summary->getCount() / 10);
            }
            if ($rating) {
                $item->addChild('rating_votes', $rating);
            }
        }
        return $item;
    }

    /**
     * Render review xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $review = Mage::getModel('Mage_Review_Model_Review')->load((int)$this->getRequest()->getParam('id', 0));
        return $this->reviewToXmlObject($review, 'review')->asNiceXml();
    }
}
