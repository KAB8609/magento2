<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ProductAlert email back in stock grid
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Block_Email_Stock extends Mage_ProductAlert_Block_Email_Abstract
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('email/stock.phtml');
    }

    /**
     * Product thumbnail image url getter
     *
     * @param Mage_Core_Model_Product $product
     * @return string
     */
    public function getThumbnailUrl($product)
    {
        return (string) $this->helper('Mage_Catalog_Helper_Image')->init($product, 'thumbnail')->resize($this->getThumbnailSize());
    }

    /**
     * Thumbnail image size getter
     *
     * @return int
     */
    public function getThumbnailSize()
    {
        return $this->getVar('product_thumbnail_image_size', 'Mage_Catalog');
    }

    /**
     * Retrive unsubscribe url for product
     *
     * @param int $productId
     * @return string
     */
    public function getProductUnsubscribeUrl($productId)
    {
        $params = $this->_getUrlParams();
        $params['product'] = $productId;
        return $this->getUrl('productalert/unsubscribe/stock', $params);
    }

    /**
     * Retrieve unsubscribe url for all products
     *
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        return $this->getUrl('productalert/unsubscribe/stockAll', $this->_getUrlParams());
    }
}
