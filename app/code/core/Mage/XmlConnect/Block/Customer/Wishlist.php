<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer wishlist xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Customer_Wishlist extends Mage_Wishlist_Block_Customer_Wishlist
{
    /**
     * Render customer wishlist xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $wishlistXmlObj = new Varien_Simplexml_Element('<wishlist></wishlist>');
        if ($this->hasWishlistItems()) {
            foreach($this->getWishlist() as $item){
                $itemXmlObj = $wishlistXmlObj->addChild('item');
                $itemXmlObj->addChild('item_id', $item->getWishlistItemId());
                $itemXmlObj->addChild('entity_id', $item->getProductId());
                $itemXmlObj->addChild('entity_type_id', $item->getTypeId());
                $itemXmlObj->addChild('name', $wishlistXmlObj->xmlentities(strip_tags($item->getName())));
                $icon = $this->helper('catalog/image')->init($item, 'small_image')
                    ->resize(Mage_XmlConnect_Block_Catalog_Product::PRODUCT_IMAGE_SMALL_RESIZE_PARAM);
                $itemXmlObj->addChild('icon', $icon);
                $itemXmlObj->addChild('comment', $wishlistXmlObj->xmlentities(strip_tags($item->getWishlistItemDescription())));
                $itemXmlObj->addChild('added_date', $wishlistXmlObj->xmlentities($this->getFormatedDate($item->getAddedAt())));
                $itemXmlObj->addChild('in_strock', (int)$item->isInStock());
                $itemXmlObj->addChild('has_options', (int)$item->getHasOptions());
                $itemXmlObj->addChild('is_salable', (int)$item->isSaleable());
                if (!$item->getRatingSummary()) {
                    Mage::getModel('review/review')
                       ->getEntitySummary($item, Mage::app()->getStore()->getId());
                }

                $itemXmlObj->addChild('rating_summary', round((int)$item->getRatingSummary()->getRatingSummary() / 10));
                $itemXmlObj->addChild('reviews_count', $item->getRatingSummary()->getReviewsCount());
            }
        }

        return $wishlistXmlObj->asNiceXml();
    }
}